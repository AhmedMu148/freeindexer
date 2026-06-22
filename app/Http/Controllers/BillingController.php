<?php

namespace App\Http\Controllers;

use App\Models\PymPayment;
use App\Models\User;
use App\Models\PymSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\CentralPaymentIntegrationService;
use App\Mail\TemplatedEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use function Symfony\Component\Clock\now;

class BillingController extends Controller
{

  public function subscribe(Request $request, CentralPaymentIntegrationService $cpService)
  {
    // check if user is logged in
    if (!Auth::check()) {
      return redirect()->route('login')->with('error', 'Please log in to subscribe.');
    }
    $user = Auth::user();

    // validate request data
    $request->validate([
      'plan_id' => 'required|numeric',
    ]);
    $plan_id = $request->plan_id;

    // check plan
    $plan = DB::table('plans')->where('id', $plan_id)->where('status', 1)->first();
    if (!$plan) {
      return redirect()->route('pricing')->with('error', 'Invalid plan selected.');
    }

    // create row in pym_payments
    $payment = PymPayment::create([
      'uid'             => $user->id,
      'plan_id'         => $plan->id,
      'gateway_id'      => null,
      'product'         => $plan->name,
      'txn'             => null,
      'amount'          => $plan->price,
      'currency_id'     => 1,
      'source_details'  => null,
      'subscription_id' => null,
      'ref'             => null,
      'status'          => 1, // Pending
    ]);

    try {
      if ($plan->type == 'monthly') {
        // Subscription flow
        $response = $cpService->createHostedSubscription([
          'plan_id' => $plan->id,
          'customer_email' => $user->email,
          'customer_name' => $user->name ?? '',
          'return_url' => route('paypal.return'), // reuse old thanks page
          'metadata' => [
            'payment_id' => $payment->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
          ]
        ]);

        $redirectUrl = $response['hosted_checkout_url'] ?? null;
      } else {
        // One-time payment (e.g. app client)
        $response = $cpService->createHostedPayment([
          'amount' => $plan->price,
          'currency' => 'USD',
          'description' => "Free Indexer {$plan->name} plan.",
          'customer_email' => $user->email,
          'customer_name' => $user->name ?? '',
          'return_url' => route('paypal.return'), // reuse old thanks page
          'metadata' => [
            'payment_id' => $payment->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
          ]
        ]);

        $redirectUrl = $response['hosted_url'] ?? null;
      }

      if (!$redirectUrl) {
        throw new \Exception('Hosted checkout URL not returned from Central Payment API.');
      }

      return redirect()->away($redirectUrl);
    } catch (\Throwable $e) {
      Log::error("Failed to initiate Central Payment checkout: " . $e->getMessage(), [
        'plan_id' => $plan->id,
        'user_id' => $user->id,
        'exception' => $e
      ]);

      // Update payment status to failed
      $payment->update(['status' => 4]); // 4 is Failed

      return redirect()->back()->with('error', 'Unable to process checkout. Please try again later.');
    }
  }

  /**
   * IPN Listener: PayPal cmd=_notify-validate
   */
  public function ipn(Request $request)
  {

    Log::info('IPN HIT', ['raw' => 'in ipn']);

    // 0) CSRF except logging
    $raw = $request->getContent();
    $verifyBody = 'cmd=_notify-validate&' . $raw;

    Log::info('IPN HIT', ['raw' => $raw]);

    $verifyUrl = config('services.paypal.mode') === 'live'
      ? 'https://ipnpb.paypal.com/cgi-bin/webscr'
      : 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';

    $res = Http::withHeaders(['User-Agent' => 'Laravel-IPN'])
      ->withBody($verifyBody, 'application/x-www-form-urlencoded')
      ->withOptions(['verify' => true])
      ->post($verifyUrl);

    Log::info('IPN HIT', ['raw' => 'check VERIFIED']);

    // $verified = trim((string) $res->body()) === 'VERIFIED';

    // if (!$verified) {
    //   Log::warning('PayPal IPN NOT VERIFIED', [
    //     'payload' => $request->all(),
    //     'resp' => $res->body(),
    //   ]);
    //   return response('invalid', 200); // 200 لتجنب retries
    // }

    Log::info('IPN HIT', ['raw' => 'pass VERIFIED']);

    // dd($request->input('payment_status'));

    // need to check if payment_status  = Completed
    if ($request->input('payment_status') !== 'Completed') {
      Log::warning('PayPal IPN not completed', ['status' => $request->input('payment_status')]);
      return response('not-completed', 200);
    }

    // 1) checks
    $mcGrossOk  = $request->filled('mc_gross') && is_numeric($request->input('mc_gross'));
    $txnIdOk    = $request->filled('txn_id');
    $businessOk = $request->input('receiver_email') === config('paypal_legacy.business');
    $currencyOk = $request->input('mc_currency') === config('paypal_legacy.currency');

    if (! $mcGrossOk || ! $txnIdOk || ! $businessOk || ! $currencyOk) {
      Log::error('PayPal IPN security checks failed', compact('businessOk', 'currencyOk'));
      return response('bad', 200);
    }

    // 2) extract
    $txnType   = $request->input('txn_type');
    $subscrId  = $request->input('subscr_id') ?? null;
    $status    = $request->input('payment_status');
    $amount    = (string) $request->input('mc_gross');
    $txn       = $request->input('txn_id');
    $uid       = (int) ($request->input('custom') ?? 0);
    $productId = (int) ($request->input('option_selection1') ?? 0);
    $paymentId = (int) ($request->input('option_selection2') ?? 0);
    $plan_id   = (int) ($request->input('option_selection3') ?? 0);

    Log::info('IPN VERIFIED OK', ['txn' => $txn, 'uid' => $uid]);

    $user   = User::where('id', $uid)->first();
    $email  = $user->email;

    // 3) check plans
    $plan = DB::table('plans')->where('id', $plan_id)->first();

    if (!$plan) return response('no-plan', 200);
    $expected = $plan->price_offer != 0 ? (string) $plan->price_offer : (string) $plan->price;
    if (bccomp($amount, $expected, 2) !== 0) {
      Log::warning('amount-mismatch', compact('amount', 'expected', 'plan_id'));
      return response('amount-mismatch', 200);
    }

    $indexerPoints    = $plan->indexer;
    $bgIndexerPoints  = $plan->bg_indexer;
    $backlinksPoints  = $plan->backlinks;

    // 4) check for duplicates
    $check = DB::table('pym_payments')->where('id', $paymentId)->where('status', '!=', 1)->first();
    if ($check) return response('duplicate', 200);

    $pym_payments_data = DB::table('pym_payments')->where('id', $paymentId)->first();

    // 5) subscription (optional)
    $subscription_id = null;
    if ($subscrId) {
      $subscriptions = DB::table('pym_subscriptions')->where('subscr_id', $subscrId)->first();

      // $start_date = gmdate('Y-m-d');
      $start_date = gmdate('Y-m-d');
      $end_date   = gmdate('Y-m-d', strtotime($start_date . ' + 32 days'));

      if (! $subscriptions) {
        $subscription_id = DB::table('pym_subscriptions')->insertGetId([
          'uid'         => $uid,
          'gateway_id'  => $pym_payments_data->gateway_id,
          'subscr_id'   => $subscrId,
          'plan_id'     => $plan_id,
          'created_at'  => gmdate('Y-m-d H:i:s'),
          'updated_at'  => gmdate('Y-m-d H:i:s'),
        ]);
      } else {
        $subscription_id = $subscriptions->id;
      }
    }

    // 6) update pym_payments
    DB::table('pym_payments')->where('id', $paymentId)->update([
      'uid'             => $uid,
      'subscription_id' => $subscription_id ?? 0,
      'plan_id'         => $plan_id,
      'txn'             => $txn,
      'amount'          => $amount,
      'status'          => 3,
      'updated_at'      => now(),
    ]);

    // 7) order
    if ($plan_id != 8) {

      DB::table('orders')->insert([
        'uid'             => $uid,
        'payment_id'      => $paymentId,
        'subscription_id' => $subscription_id ?? 0,
        'plan_id'         => $plan_id,
        'indexer'         => $plan->indexer,
        'bg_indexer'      => $plan->bg_indexer,
        'backlinks'       => $plan->backlinks,
        'start'           => $start_date,
        'end'             => $end_date,
        'status_id'       => 2,
        'created_at'      => now(),
        'updated_at'      => now(),
      ]);

      // insert or update user points
      $record = DB::table('indexer_points')->where('uid', $uid)->first();
      if ($record) {
        $newPoints = $record->points + $indexerPoints;
        DB::table('indexer_points')
          ->where('uid', $uid)
          ->update(['points' => $newPoints]);
      } else {
        DB::table('indexer_points')->insert([
          'uid'        => $uid,
          'points'     => $indexerPoints,
          'used'       => 0,
          'created_at' => now(),
          'updated_at' => now(),
        ]);
      }

      $record = DB::table('bg_indexer_points')->where('uid', $uid)->first();
      if ($record) {
        $newPoints = $record->points + $bgIndexerPoints;
        DB::table('bg_indexer_points')
          ->where('uid', $uid)
          ->update(['points' => $newPoints]);
      } else {
        DB::table('bg_indexer_points')->insert([
          'uid'        => $uid,
          'points'     => $bgIndexerPoints,
          'used'       => 0,
          'created_at' => now(),
          'updated_at' => now(),
        ]);
      }

      $record = DB::table('backlinks_points')->where('uid', $uid)->first();
      if ($record) {
        $newPoints = $record->points + $backlinksPoints;
        DB::table('backlinks_points')
          ->where('uid', $uid)
          ->update(['points' => $newPoints]);
      } else {
        DB::table('backlinks_points')->insert([
          'uid'        => $uid,
          'points'     => $backlinksPoints,
          'used'       => 0,
          'created_at' => now(),
          'updated_at' => now(),
        ]);
      }
    } else {

      // $key = md5(random_int(0, 1000));
      
      // DB::table('app')->insert([
      //   'uid'        => $uid,
      //   'payment_id' => $paymentId,
      //   'key'        => $key,
      //   'created_at' => now(),
      //   'updated_at' => now(),
      // ]);

      /** @var AppClient $app */
      $key = Str::random(32);
      $app = app(AppClient::class);
      $key = $app->createKeyRow($uid, $paymentId, $key);
      $app->sendKeyApi($uid, $key);
    }

    // Mail::to($email)->send(
    //   new TemplatedEmail(
    //     template: 'payment-received',
    //     subjectLine: 'Payment received successfully',
    //     data: [
    //       'invoiceNo'  => 'INV-2025-0012',
    //       'amount'     => $amount,
    //       'method'     => 'Visa **** 1234',
    //       'invoiceUrl' => url('/invoice/INV-2025-0012'),
    //       'billingUrl' => url('/billing'),
    //     ]
    //   )
    // );

    Log::info('PayPal IPN VERIFIED', compact('txnType', 'subscrId', 'amount', 'txn', 'uid', 'productId', 'paymentId'));
    return response('done', 200);
  }

  public function return(Request $r)
  {
    return view('paypal.thanks');
  }

  public function cancel()
  {
    return view('paypal.cancel');
  }
}
