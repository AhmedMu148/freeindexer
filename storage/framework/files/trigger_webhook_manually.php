<?php
require 'c:/xampp/htdocs/freeindexer/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/freeindexer/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CentralPaymentIntegrationService;
use Illuminate\Support\Str;

$paymentId = 3;
$payment = DB::table('pym_payments')->find($paymentId);
if (!$payment) {
    die("Payment ID $paymentId not found.\n");
}

if ($payment->status == 3) {
    die("Payment ID $paymentId is already completed.\n");
}

echo "Simulating webhook for Payment ID: {$payment->id}, User: {$payment->uid}, Plan: {$payment->plan_id}\n";

$txHash = 'tx_manual_' . Str::random(12);
$subHash = 'sub_manual_' . Str::random(12);

$payload = [
    'event_id' => 'evt_manual_' . Str::random(12),
    'event' => 'transaction.completed',
    'data' => [
        'transaction' => [
            'id' => 'tx_' . Str::random(10),
            'transaction_hash' => $txHash,
            'amount' => (string) $payment->amount,
            'currency' => 'USD',
            'metadata' => [
                'payment_id' => $payment->id,
                'user_id' => (int) $payment->uid,
                'plan_id' => (int) $payment->plan_id,
            ],
            'subscription' => [
                'subscription_hash' => $subHash,
            ]
        ]
    ]
];

// Create the WebhookEvent row to log it
DB::table('webhook_events')->insert([
    'provider' => 'central_payment',
    'event_id' => $payload['event_id'],
    'event_type' => $payload['event'],
    'payload' => json_encode($payload),
    'status' => 'processing',
    'created_at' => now(),
    'updated_at' => now(),
]);

try {
    $service = app(CentralPaymentIntegrationService::class);
    $service->processWebhook($payload);

    DB::table('webhook_events')
        ->where('event_id', $payload['event_id'])
        ->update(['status' => 'completed', 'updated_at' => now()]);

    echo "Webhook processed successfully!\n";
} catch (\Throwable $e) {
    DB::table('webhook_events')
        ->where('event_id', $payload['event_id'])
        ->update(['status' => 'failed', 'error_message' => $e->getMessage(), 'updated_at' => now()]);

    echo "Error processing webhook: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
