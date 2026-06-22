<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\IndexerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FeedbackController;
use App\Mail\TemplatedEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/', function () {
  return view('home');
})->name('/');

Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');

Route::get('/buy-app', [PricingController::class, 'buyApp'])->name('buy-app');
// Route::get('/buy-app.php', [PricingController::class, 'buyApp'])->name('buy-app.php');

Route::get('/download-app', function () {
  return view('download-app');
})->name('download-app');

// Route::get('/download-app.php', function () {
//   return view('download-app');
// })->name('download-app.php');

Route::get('/about', function () {
  return view('about');
})->name('about');

Route::view('/contact', 'contact')->name('contact.show');
Route::post('/contact', [ContactController::class, 'store'])
  ->name('contact.store');

Route::get('/feedback-app', function () {
  return view('feedback-app');
})->name('feedback-app');

// Route::get('/feedback-app.php', function () {
//   return view('feedback-app');
// })->name('feedback-app.php');

Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

Route::post('/process',       [IndexerController::class, 'processPage'])->name('indexer.processPage');
Route::get('/process', [IndexerController::class, 'processReload'])->name('indexer.process.reload');

Route::post('/indexer/submit', [IndexerController::class, 'submit'])->name('indexer.submit');
Route::post('/indexer/step',  [IndexerController::class, 'step'])->name('indexer.step');

// Redirect to Filament dashboard login
// Route::get('/login', function () {
//   return redirect('/dashboard/login');
// })->name('login');

Route::get('/login', function (Request $request) {
  $redirect = $request->query('redirect', url()->previous());
  return redirect()->route('filament.dashboard.auth.login', [
    'redirect' => $redirect,
  ]);
})->name('login');

// Route::get('/login', function (Request $request) {
//     $redirect = $request->query('redirect', url()->previous());

//     session()->put('after_login_redirect', $redirect);

//     return redirect()->route('filament.dashboard.auth.login');
// })->name('login');

// Redirect to Filament dashboard registration
Route::get('/register', function () {
  return redirect('/dashboard/register');
})->name('register');

// Redirect to Filament dashboard profile
Route::get('/profile', function () {
  return redirect('/dashboard/profile');
})->name('profile');

Route::middleware('auth')->group(function () {
  Route::post('/billing/subscribe', [BillingController::class, 'subscribe'])->name('billing.subscribe');
});

Route::get('/wallet/success',     [BillingController::class, 'return'])->name('wallet.success');
Route::get('/paypal/return',     [BillingController::class, 'return'])->name('paypal.return');
Route::get('/paypal/cancel',     [BillingController::class, 'cancel'])->name('paypal.cancel');

Route::get('/background-indexer/{path}', function (string $path) {
  $fullPath = "background-indexer/{$path}";
  abort_unless(Storage::disk('local')->exists($fullPath), 404);
  return Storage::download($fullPath);
})->where('path', '.*')
  ->name('background-indexer.download');

Route::middleware('auth')->get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
  $request->fulfill();
  return redirect('/dashboard')->with('status', 'Email verified successfully!');
})->middleware('signed')->name('verification.verify');

// Route::middleware('auth')->post('/email/verification-notification', function (Request $request) {
//   $request->user()->sendEmailVerificationNotification();
//   return back()->with('status', 'verification-link-sent');
// })->name('verification.send');

Route::post(
  'dashboard/email-verification/resend',
  function (Request $request) {
    $user = $request->user();
    if (! $user || ! $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) {
      abort(403);
    }
    if ($user->hasVerifiedEmail()) {
      return back();
    }
    $user->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
  }
)
  ->middleware(['web', 'auth', 'throttle:6,1'])
  ->name('filament.dashboard.auth.email-verification.resend');

Route::middleware(['web', 'auth'])->get('/tickets/sso', [\App\Http\Controllers\SsoController::class, 'redirect'])
    ->name('tickets.sso');

Route::get('/pages/{slug}', [App\Http\Controllers\CmsPageController::class, 'show'])
    ->where('slug', '[A-Za-z0-9\-]+')
    ->name('cms.page.show');

Route::fallback([App\Http\Controllers\CmsPageController::class, 'showAtRoot'])
    ->name('cms.page.fallback');
