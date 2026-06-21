<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PymPayment;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Support\SubscriptionBillingIdentityResolver;
use App\Support\PaymentCycleIdentity;
use App\Services\AppClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CentralPaymentIntegrationService
{
    protected SubscriptionBillingIdentityResolver $identityResolver;

    private string $baseUrl;
    private string $apiKey;
    private string $secretKey;
    private string $webhookSecret;
    private string $apiVersion;
    private int $timeout;
    private bool $verifySSL;

    public function __construct(SubscriptionBillingIdentityResolver $identityResolver)
    {
        $this->identityResolver = $identityResolver;
        $this->baseUrl = rtrim(config('services.central_payment.base_url', 'https://billing.flare99.com'), '/');
        $this->apiKey = config('services.central_payment.api_key', '');
        $this->secretKey = config('services.central_payment.secret_key', '');
        $this->webhookSecret = config('services.central_payment.webhook_secret', $this->secretKey);
        $this->apiVersion = config('services.central_payment.api_version', 'v1');
        $this->timeout = (int) config('services.central_payment.timeout', 30);
        $this->verifySSL = (bool) config('services.central_payment.verify_ssl', true);
    }

    /**
     * Creates a hosted payment session and returns checkouts URL
     */
    public function createHostedPayment(array $paymentData): array
    {
        $endpoint = "/api/{$this->apiVersion}/payments/hosted-url";

        $requiredFields = ['amount', 'currency', 'description', 'customer_email'];
        foreach ($requiredFields as $field) {
            if (!isset($paymentData[$field]) || empty($paymentData[$field])) {
                throw new \Exception("Missing required field: {$field}");
            }
        }

        $payload = [
            'amount' => (string) $paymentData['amount'],
            'currency' => strtoupper($paymentData['currency']),
            'description' => $paymentData['description'],
            'customer_email' => $paymentData['customer_email'],
        ];

        if (!empty($paymentData['external_reference'])) {
            $payload['external_reference'] = $paymentData['external_reference'];
        }
        if (!empty($paymentData['customer_name'])) {
            $payload['customer_name'] = $paymentData['customer_name'];
        }
        if (!empty($paymentData['customer_phone'])) {
            $payload['customer_phone'] = $paymentData['customer_phone'];
        }
        if (isset($paymentData['return_url'])) {
            $payload['return_url'] = $paymentData['return_url'];
        }
        if (isset($paymentData['metadata'])) {
            $payload['metadata'] = $paymentData['metadata'];
        }

        $response = $this->makeAuthenticatedRequest('POST', $endpoint, $payload);

        if (isset($response['transaction_id'])) {
            $response['transaction_hash'] = $response['transaction_id'];
        }
        if (isset($response['checkout_url'])) {
            $response['hosted_url'] = $response['checkout_url'];
        }

        return $response;
    }

    /**
     * Creates a hosted subscription checkout session
     */
    public function createHostedSubscription(array $subscriptionData): array
    {
        $endpoint = "/api/{$this->apiVersion}/subscriptions/hosted-inline";

        if (!isset($subscriptionData['plan_id']) || empty($subscriptionData['plan_id'])) {
            throw new \Exception('Missing required field: plan_id');
        }
        if (!isset($subscriptionData['customer_email']) || empty($subscriptionData['customer_email'])) {
            throw new \Exception('Missing required field: customer_email');
        }

        $plan = DB::table('plans')->where('id', $subscriptionData['plan_id'])->first();
        if (!$plan) {
            throw new \Exception('Plan not found: ' . $subscriptionData['plan_id']);
        }

        $inlinePlan = [
            'name' => $plan->name,
            'description' => $subscriptionData['description'] ?? $plan->name . ' Subscription',
            'amount' => $plan->price,
            'currency' => 'USD',
            'interval' => 'month',
            'interval_count' => 1,
        ];

        $payload = [
            'inline_plan' => $inlinePlan,
            'customer_email' => $subscriptionData['customer_email'],
        ];

        if (!empty($subscriptionData['customer_name'])) {
            $payload['customer_name'] = $subscriptionData['customer_name'];
        }
        if (!empty($subscriptionData['customer_phone'])) {
            $payload['customer_phone'] = $subscriptionData['customer_phone'];
        }
        if (isset($subscriptionData['return_url'])) {
            $payload['return_url'] = $subscriptionData['return_url'];
        }

        $metadata = $subscriptionData['metadata'] ?? [];
        $metadata['plan_id'] = $plan->id;
        $payload['metadata'] = $metadata;

        $response = $this->makeAuthenticatedRequest('POST', $endpoint, $payload);

        if (isset($response['data']['subscription_hash'])) {
            $response['subscription_hash'] = $response['data']['subscription_hash'];
        }
        if (isset($response['data']['hosted_checkout_url'])) {
            $response['hosted_checkout_url'] = $response['data']['hosted_checkout_url'];
        }

        return $response;
    }

    /**
     * Make authenticated API request with HMAC-SHA256 signature
     */
    private function makeAuthenticatedRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . $endpoint;
        $timestamp = time();
        $body = '';

        if (!empty($data) && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $body = json_encode($data, JSON_UNESCAPED_SLASHES);
        }

        $signature = base64_encode(hash_hmac('sha256', "{$timestamp}.{$body}", $this->secretKey, true));

        $headers = [
            'X-Api-Key' => $this->apiKey,
            'X-Signature' => $signature,
            'X-Timestamp' => (string) $timestamp,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'CentralPaymentIntegration/1.0',
        ];

        $httpClient = Http::withHeaders($headers)->timeout($this->timeout);
        if (!$this->verifySSL) {
            $httpClient = $httpClient->withOptions(['verify' => false]);
        }

        $response = match ($method) {
            'GET' => $httpClient->get($url),
            'POST' => $httpClient->send('POST', $url, ['body' => $body]),
            'PUT' => $httpClient->send('PUT', $url, ['body' => $body]),
            'DELETE' => $httpClient->delete($url),
            default => throw new \Exception("Unsupported HTTP method: {$method}")
        };

        if (!$response->successful()) {
            $errorBody = $response->json();
            $errorMessage = $errorBody['message'] ?? $errorBody['error'] ?? 'Unknown error';
            throw new \Exception("Central Payment API request failed ({$response->status()}): {$errorMessage}");
        }

        return $response->json() ?? [];
    }

    /**
     * Verify the webhook signature against the raw payload
     */
    public function verifyWebhookSignature(\Illuminate\Http\Request $request): bool
    {
        $signature = $request->header('X-Central-Payment-Signature')
            ?? $request->header('X-Webhook-Signature')
            ?? $request->header('x-central-payment-signature')
            ?? $request->header('x-webhook-signature');

        if (!$signature) {
            Log::warning('Central Payment webhook signature header missing.');
            return false;
        }

        $rawPayload = $request->getContent();
        $webhookSecret = config('services.central_payment.webhook_secret')
            ?? config('services.central_payment.secret_key')
            ?? config('services.central_payment.secret');

        if (!$webhookSecret) {
            Log::error('Central Payment webhook secret is not configured.');
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $rawPayload, $webhookSecret);
        return hash_equals($expectedSignature, strtolower($signature));
    }

    /**
     * Process the webhook payload
     */
    public function processWebhook(array $payload): void
    {
        $event = $payload['event'] ?? null;
        $eventId = $payload['event_id'] ?? $payload['id'] ?? null;

        Log::info("Processing Central Payment webhook event: {$event} (ID: {$eventId})");

        switch ($event) {
            case 'transaction.completed':
                $this->handleTransactionCompleted($payload);
                break;

            case 'subscription.renewed':
            case 'subscription.payment.succeeded':
                $this->handleSubscriptionRenewed($payload);
                break;

            case 'subscription.cancelled':
            case 'subscription.expired':
                $this->handleSubscriptionCancelledOrExpired($payload);
                break;

            default:
                Log::info("Unhandled event type: {$event}");
                break;
        }
    }

    /**
     * Handle transaction.completed event
     */
    protected function handleTransactionCompleted(array $payload): void
    {
        $data = $payload['data'] ?? [];
        $transaction = $data['transaction'] ?? $data;

        $metadata = $transaction['metadata'] ?? [];
        $paymentId = $metadata['payment_id'] ?? null;
        $uid = $metadata['user_id'] ?? null;
        $planId = $metadata['plan_id'] ?? null;

        if (!$paymentId) {
            Log::warning('Central Payment transaction.completed: payment_id not found in metadata.');
            return;
        }

        DB::transaction(function () use ($paymentId, $uid, $planId, $transaction, $metadata) {
            $payment = PymPayment::lockForUpdate()->find($paymentId);
            if (!$payment) {
                Log::warning("Central Payment: Payment row ID {$paymentId} not found.");
                return;
            }

            if ($payment->status == 3) {
                Log::info("Central Payment: Payment ID {$paymentId} already marked completed.");
                return;
            }

            $plan = DB::table('plans')->where('id', $planId ?? $payment->plan_id)->first();
            if (!$plan) {
                Log::error("Central Payment: Plan ID {$planId} not found.");
                return;
            }

            $subscriptionData = $transaction['subscription'] ?? null;
            $subscriptionId = null;
            if ($subscriptionData && isset($subscriptionData['subscription_hash'])) {
                $subscrId = $subscriptionData['subscription_hash'];
                
                $sub = DB::table('pym_subscriptions')->where('subscr_id', $subscrId)->first();
                if (!$sub) {
                    $subscriptionId = DB::table('pym_subscriptions')->insertGetId([
                        'uid' => $payment->uid,
                        'gateway_id' => $payment->gateway_id,
                        'subscr_id' => $subscrId,
                        'plan_id' => $plan->id,
                        'created_at' => gmdate('Y-m-d H:i:s'),
                        'updated_at' => gmdate('Y-m-d H:i:s'),
                    ]);
                } else {
                    $subscriptionId = $sub->id;
                }
            }

            $payment->update([
                'subscription_id' => $subscriptionId ?? $payment->subscription_id ?? 0,
                'txn' => $transaction['transaction_hash'] ?? $transaction['id'] ?? null,
                'payment_hash' => $transaction['transaction_hash'] ?? null,
                'metadata' => $metadata,
                'status' => 3,
                'completed_at' => now(),
            ]);

            $existingOrder = Order::where('payment_id', $paymentId)->first();
            if ($existingOrder) {
                Log::info("Central Payment: Order already exists for payment ID {$paymentId}.");
                return;
            }

            $startDate = gmdate('Y-m-d');
            $endDate = gmdate('Y-m-d', strtotime($startDate . ' + 32 days'));

            if ($plan->id != 8) {
                Order::create([
                    'uid' => $payment->uid,
                    'payment_id' => $payment->id,
                    'subscription_id' => $subscriptionId ?? 0,
                    'plan_id' => $plan->id,
                    'indexer' => $plan->indexer,
                    'bg_indexer' => $plan->bg_indexer,
                    'backlinks' => $plan->backlinks,
                    'start' => $startDate,
                    'end' => $endDate,
                    'status_id' => 2,
                    'billing_cycle_count' => 1,
                ]);

                $this->addPoints($payment->uid, $plan->indexer, $plan->bg_indexer, $plan->backlinks);
            } else {
                $key = Str::random(32);
                $appClient = app(AppClient::class);
                $key = $appClient->createKeyRow($payment->uid, $payment->id, $key);
                $appClient->sendKeyApi($payment->uid, $key);
            }
        });
    }

    /**
     * Handle subscription.renewed event
     */
    protected function handleSubscriptionRenewed(array $payload): void
    {
        $data = $payload['data'] ?? [];
        $subscriptionData = $data['subscription'] ?? $data;

        if (!isset($subscriptionData['subscription_hash'])) {
            Log::warning('Central Payment subscription.renewed: subscription_hash not found.');
            return;
        }

        $subscriptionHash = $subscriptionData['subscription_hash'];

        $cycleIdentity = $this->identityResolver->fromSubscriptionRenewedPayload(
            $subscriptionHash,
            $subscriptionData,
            $payload
        );

        $metadata = $cycleIdentity->metadata();
        $paymentId = $metadata['payment_id'] ?? null;
        $uid = $metadata['user_id'] ?? null;
        $planId = $metadata['plan_id'] ?? null;

        Log::info("Handling subscription.renewed cycle: " . ($cycleIdentity->billingCycleCount ?? 'unknown') . " for sub hash: {$subscriptionHash}");

        DB::transaction(function () use ($cycleIdentity, $subscriptionHash, $paymentId, $uid, $planId) {
            $sub = DB::table('pym_subscriptions')->where('subscr_id', $subscriptionHash)->first();
            if (!$sub) {
                $payment = PymPayment::find($paymentId);
                $subId = DB::table('pym_subscriptions')->insertGetId([
                    'uid' => $uid ?? ($payment ? $payment->uid : 0),
                    'gateway_id' => $payment ? $payment->gateway_id : 2,
                    'subscr_id' => $subscriptionHash,
                    'plan_id' => $planId ?? ($payment ? $payment->plan_id : null),
                    'created_at' => gmdate('Y-m-d H:i:s'),
                    'updated_at' => gmdate('Y-m-d H:i:s'),
                ]);
                $sub = DB::table('pym_subscriptions')->where('id', $subId)->first();
            }

            $plan = DB::table('plans')->where('id', $planId ?? $sub->plan_id)->first();
            if (!$plan) {
                Log::error("Central Payment: Plan not found for subscription renewal.");
                return;
            }

            $cycle = $cycleIdentity->billingCycleCount;
            if ($cycle !== null && $cycle > 0) {
                $existingCycle = Order::where('subscription_id', $sub->id)
                    ->where('billing_cycle_count', $cycle)
                    ->first();

                if ($existingCycle) {
                    Log::info("Central Payment: Webhook cycle {$cycle} already processed for subscription hash {$subscriptionHash}.");
                    return;
                }
            }

            $rootOrder = Order::where('subscription_id', $sub->id)
                ->where(function($q) {
                    $q->whereNull('extends')->orWhere('extends', 0);
                })
                ->orderBy('id', 'asc')
                ->first();

            $startDate = $cycleIdentity->periodStart ? $cycleIdentity->periodStart->toDateString() : gmdate('Y-m-d');
            $endDate = $cycleIdentity->periodEnd ? $cycleIdentity->periodEnd->toDateString() : gmdate('Y-m-d', strtotime($startDate . ' + 32 days'));

            $newOrder = Order::create([
                'uid' => $uid ?? $sub->uid,
                'payment_id' => $paymentId ?? ($rootOrder ? $rootOrder->payment_id : null),
                'subscription_id' => $sub->id,
                'plan_id' => $plan->id,
                'indexer' => $plan->indexer,
                'bg_indexer' => $plan->bg_indexer,
                'backlinks' => $plan->backlinks,
                'start' => $startDate,
                'end' => $endDate,
                'status_id' => 2,
                'extends' => $rootOrder ? $rootOrder->id : null,
                'billing_cycle_count' => $cycle ?? 1,
            ]);

            if ($plan->id != 8) {
                $this->addPoints($sub->uid, $plan->indexer, $plan->bg_indexer, $plan->backlinks);
            } else {
                $key = Str::random(32);
                $appClient = app(AppClient::class);
                $key = $appClient->createKeyRow($sub->uid, $newOrder->payment_id, $key);
                $appClient->sendKeyApi($sub->uid, $key);
            }
        });
    }

    /**
     * Handle subscription cancellation or expiration
     */
    protected function handleSubscriptionCancelledOrExpired(array $payload): void
    {
        $data = $payload['data'] ?? [];
        $subscriptionData = $data['subscription'] ?? $data;

        if (!isset($subscriptionData['subscription_hash'])) {
            return;
        }

        $subscriptionHash = $subscriptionData['subscription_hash'];
        $sub = DB::table('pym_subscriptions')->where('subscr_id', $subscriptionHash)->first();
        if (!$sub) {
            return;
        }

        DB::table('orders')->where('subscription_id', $sub->id)->update([
            'status_id' => 3,
            'updated_at' => now(),
        ]);

        Log::info("Central Payment: Subscription {$subscriptionHash} updated to cancelled.");
    }

    /**
     * Fulfill/Add points helper
     */
    protected function addPoints(int $uid, int $indexerPoints, int $bgIndexerPoints, int $backlinksPoints): void
    {
        $record = DB::table('indexer_points')->where('uid', $uid)->first();
        if ($record) {
            DB::table('indexer_points')
                ->where('uid', $uid)
                ->update(['points' => $record->points + $indexerPoints, 'updated_at' => now()]);
        } else {
            DB::table('indexer_points')->insert([
                'uid' => $uid,
                'points' => $indexerPoints,
                'used' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $record = DB::table('bg_indexer_points')->where('uid', $uid)->first();
        if ($record) {
            DB::table('bg_indexer_points')
                ->where('uid', $uid)
                ->update(['points' => $record->points + $bgIndexerPoints, 'updated_at' => now()]);
        } else {
            DB::table('bg_indexer_points')->insert([
                'uid' => $uid,
                'points' => $bgIndexerPoints,
                'used' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $record = DB::table('backlinks_points')->where('uid', $uid)->first();
        if ($record) {
            DB::table('backlinks_points')
                ->where('uid', $uid)
                ->update(['points' => $record->points + $backlinksPoints, 'updated_at' => now()]);
        } else {
            DB::table('backlinks_points')->insert([
                'uid' => $uid,
                'points' => $backlinksPoints,
                'used' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
