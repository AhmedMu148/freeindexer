<?php

use App\Models\User;
use App\Models\Order;
use App\Models\PymPayment;
use App\Models\WebhookEvent;
use App\Services\AppClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    config([
        'services.central_payment.secret_key' => 'test_webhook_secret_key',
        'services.central_payment.webhook_secret' => 'test_webhook_secret_key',
    ]);

    // Seed order_status table
    DB::table('order_status')->insert([
        ['id' => 1, 'name' => 'Pending'],
        ['id' => 2, 'name' => 'Active'],
        ['id' => 3, 'name' => 'Cancelled'],
    ]);

    // Seed plans table
    DB::table('plans')->insert([
        [
            'id' => 1,
            'name' => 'Monthly Plan',
            'type' => 'monthly',
            'price' => '29.99',
            'price_offer' => '0.00',
            'indexer' => 1000,
            'bg_indexer' => 500,
            'backlinks' => 250,
            'indexer_list' => 0,
            'bg_indexer_list' => 0,
            'trial' => 0,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'id' => 8,
            'name' => 'App Plan',
            'type' => 'app',
            'price' => '99.00',
            'price_offer' => '0.00',
            'indexer' => 0,
            'bg_indexer' => 0,
            'backlinks' => 0,
            'indexer_list' => 0,
            'bg_indexer_list' => 0,
            'trial' => 0,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    ]);

    // Create user
    $this->user = User::factory()->create([
        'id' => 456,
        'email' => 'user@example.com'
    ]);
});

function getSignatureHeader(string $payload, string $secret): string
{
    return hash_hmac('sha256', $payload, $secret);
}

test('createHostedPayment initiates a payment checkout URL', function () {
    Http::fake([
        '*' => Http::response([
            'success' => true,
            'transaction_id' => 'tx_abc123',
            'checkout_url' => 'https://billing.flare99.com/hosted-payment/tx_abc123',
        ], 200)
    ]);

    $service = app(\App\Services\CentralPaymentIntegrationService::class);
    $response = $service->createHostedPayment([
        'amount' => '29.99',
        'currency' => 'USD',
        'description' => 'Test Payment',
        'customer_email' => 'user@example.com',
        'external_reference' => 'ref_123',
    ]);

    expect($response['transaction_hash'])->toBe('tx_abc123')
        ->and($response['hosted_url'])->toBe('https://billing.flare99.com/hosted-payment/tx_abc123');
});

test('createHostedSubscription initiates a subscription checkout URL', function () {
    Http::fake([
        '*' => Http::response([
            'success' => true,
            'data' => [
                'subscription_hash' => 'sub_xyz456',
                'hosted_checkout_url' => 'https://billing.flare99.com/hosted-subscription/sub_xyz456',
            ]
        ], 200)
    ]);

    $service = app(\App\Services\CentralPaymentIntegrationService::class);
    $response = $service->createHostedSubscription([
        'plan_id' => 1,
        'customer_email' => 'user@example.com',
        'return_url' => 'https://example.com/return',
    ]);

    expect($response['subscription_hash'])->toBe('sub_xyz456')
        ->and($response['hosted_checkout_url'])->toBe('https://billing.flare99.com/hosted-subscription/sub_xyz456');
});

test('webhook fails signature verification', function () {
    $payload = json_encode(['event' => 'transaction.completed', 'id' => 'evt_123']);
    
    $response = $this->withHeaders([
        'X-Central-Payment-Signature' => 'invalid_signature',
    ])->postJson('/api/central-payment/webhook', json_decode($payload, true));

    $response->assertStatus(400);
});

test('webhook handles transaction.completed and credits points', function () {
    $payment = PymPayment::create([
        'uid' => $this->user->id,
        'plan_id' => 1,
        'gateway_id' => 2,
        'product' => 'Monthly Plan',
        'txn' => '',
        'amount' => 29.99,
        'currency_id' => 1,
        'status' => 1,
    ]);

    $payloadData = [
        'event_id' => 'evt_tx_completed_123',
        'event' => 'transaction.completed',
        'data' => [
            'transaction' => [
                'id' => 'cp_tx_123',
                'transaction_hash' => 'hash_tx_123',
                'amount' => '29.99',
                'currency' => 'USD',
                'metadata' => [
                    'payment_id' => $payment->id,
                    'user_id' => $this->user->id,
                    'plan_id' => 1,
                ]
            ]
        ]
    ];

    $payload = json_encode($payloadData);
    $signature = getSignatureHeader($payload, 'test_webhook_secret_key');

    $response = $this->withHeaders([
        'X-Central-Payment-Signature' => $signature,
    ])->postJson('/api/central-payment/webhook', $payloadData);

    $response->assertStatus(200);

    // Assert payment updated
    $payment->refresh();
    expect($payment->status)->toBe("3")
        ->and($payment->txn)->toBe('hash_tx_123')
        ->and($payment->payment_hash)->toBe('hash_tx_123');

    // Assert Order created
    $order = Order::where('payment_id', $payment->id)->first();
    expect($order)->not->toBeNull()
        ->and($order->uid)->toBe((string) $this->user->id)
        ->and($order->plan_id)->toBe(1)
        ->and($order->status_id)->toBe(2) // Active
        ->and($order->billing_cycle_count)->toBe(1);

    // Assert points credited
    $indexerPoints = DB::table('indexer_points')->where('uid', $this->user->id)->first();
    expect($indexerPoints)->not->toBeNull()
        ->and($indexerPoints->points)->toBe(1000);

    $bgPoints = DB::table('bg_indexer_points')->where('uid', $this->user->id)->first();
    expect($bgPoints)->not->toBeNull()
        ->and($bgPoints->points)->toBe(500);

    $backlinksPoints = DB::table('backlinks_points')->where('uid', $this->user->id)->first();
    expect($backlinksPoints)->not->toBeNull()
        ->and($backlinksPoints->points)->toBe(250);
});

test('webhook handles transaction.completed for app client license key generation', function () {
    $payment = PymPayment::create([
        'uid' => $this->user->id,
        'plan_id' => 8,
        'gateway_id' => 2,
        'product' => 'App Plan',
        'txn' => '',
        'amount' => 99.00,
        'currency_id' => 1,
        'status' => 1,
    ]);

    $payloadData = [
        'event_id' => 'evt_tx_app_completed',
        'event' => 'transaction.completed',
        'data' => [
            'transaction' => [
                'id' => 'cp_tx_app',
                'transaction_hash' => 'hash_tx_app',
                'amount' => '99.00',
                'currency' => 'USD',
                'metadata' => [
                    'payment_id' => $payment->id,
                    'user_id' => $this->user->id,
                    'plan_id' => 8,
                ]
            ]
        ]
    ];

    $payload = json_encode($payloadData);
    $signature = getSignatureHeader($payload, 'test_webhook_secret_key');

    // Mock AppClient service
    $mockAppClient = Mockery::mock(AppClient::class);
    $mockAppClient->shouldReceive('createKeyRow')
        ->once()
        ->with($this->user->id, $payment->id, Mockery::type('string'))
        ->andReturn('generated_lic_key_123');
    $mockAppClient->shouldReceive('sendKeyApi')
        ->once()
        ->with($this->user->id, 'generated_lic_key_123');

    $this->app->instance(AppClient::class, $mockAppClient);

    $response = $this->withHeaders([
        'X-Central-Payment-Signature' => $signature,
    ])->postJson('/api/central-payment/webhook', $payloadData);

    $response->assertStatus(200);

    $payment->refresh();
    expect($payment->status)->toBe("3");
});

test('webhook handles subscription.renewed and enforces idempotency', function () {
    // Create pre-existing subscription and root order
    $subId = DB::table('pym_subscriptions')->insertGetId([
        'uid' => $this->user->id,
        'gateway_id' => 2,
        'subscr_id' => 'sub_hash_123',
        'plan_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $payment = PymPayment::create([
        'uid' => $this->user->id,
        'plan_id' => 1,
        'gateway_id' => 2,
        'product' => 'Monthly Plan',
        'txn' => 'hash_tx_123',
        'amount' => 29.99,
        'currency_id' => 1,
        'subscription_id' => $subId,
        'status' => 3,
    ]);

    $rootOrder = Order::create([
        'uid' => $this->user->id,
        'payment_id' => $payment->id,
        'subscription_id' => $subId,
        'plan_id' => 1,
        'indexer' => 1000,
        'bg_indexer' => 500,
        'backlinks' => 250,
        'start' => now()->subDays(30),
        'end' => now(),
        'status_id' => 2,
        'billing_cycle_count' => 1,
    ]);

    $payloadData = [
        'event_id' => 'evt_sub_renewed_123',
        'event' => 'subscription.renewed',
        'data' => [
            'subscription' => [
                'subscription_hash' => 'sub_hash_123',
                'billing_cycle_count' => 2,
                'current_period_start' => now()->toIso8601String(),
                'current_period_end' => now()->addDays(30)->toIso8601String(),
                'amount' => '29.99',
                'currency' => 'USD',
                'metadata' => [
                    'payment_id' => $payment->id,
                    'user_id' => $this->user->id,
                    'plan_id' => 1,
                ]
            ]
        ]
    ];

    $payload = json_encode($payloadData);
    $signature = getSignatureHeader($payload, 'test_webhook_secret_key');

    // 1st request processing renewal cycle
    $response = $this->withHeaders([
        'X-Central-Payment-Signature' => $signature,
    ])->postJson('/api/central-payment/webhook', $payloadData);

    $response->assertStatus(200);

    // Verify 2nd order is created (extends the root order)
    $cycleOrder = Order::where('subscription_id', $subId)->where('billing_cycle_count', 2)->first();
    expect($cycleOrder)->not->toBeNull()
        ->and($cycleOrder->extends)->toBe($rootOrder->id)
        ->and($cycleOrder->plan_id)->toBe(1)
        ->and($cycleOrder->status_id)->toBe(2);

    // Verify points added to user balance (1000 original + 1000 renewal = 2000)
    $indexerPoints = DB::table('indexer_points')->where('uid', $this->user->id)->first();
    expect($indexerPoints->points)->toBe(1000); // 1000 from transaction.completed was not executed in this test, so it's just 1000 from this single renewal call.

    // Send duplicate webhook event (same event_id)
    $duplicateResponse = $this->withHeaders([
        'X-Central-Payment-Signature' => $signature,
    ])->postJson('/api/central-payment/webhook', $payloadData);

    $duplicateResponse->assertStatus(200);
    $duplicateResponse->assertSee('Event already processed');

    // Verify only one cycle 2 order was created
    $cycleOrdersCount = Order::where('subscription_id', $subId)->where('billing_cycle_count', 2)->count();
    expect($cycleOrdersCount)->toBe(1);
});
