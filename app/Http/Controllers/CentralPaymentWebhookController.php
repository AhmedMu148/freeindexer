<?php

namespace App\Http\Controllers;

use App\Services\CentralPaymentIntegrationService;
use App\Models\WebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CentralPaymentWebhookController extends Controller
{
    public function handle(Request $request, CentralPaymentIntegrationService $integrationService)
    {
        Log::info("Incoming Central Payment Webhook received.");

        // 1. Verify webhook signature
        if (!$integrationService->verifyWebhookSignature($request)) {
            Log::warning("Webhook signature verification failed.");
            return response('Invalid signature', 400);
        }

        $payload = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("Failed to parse JSON payload: " . json_last_error_msg());
            return response('Invalid JSON', 400);
        }
        dd($payload);
        $eventId = $payload['event_id'] ?? $payload['id'] ?? null;
        $eventType = $payload['event'] ?? 'unknown';

        if (!$eventId) {
            Log::warning("Webhook missing event_id or id.");
            return response('Missing event identifier', 400);
        }

        // 2. Check for duplicate webhook event to guarantee idempotency
        $existingEvent = WebhookEvent::where('provider', 'central_payment')
            ->where('event_id', $eventId)
            ->first();

        if ($existingEvent) {
            Log::info("Central Payment webhook event {$eventId} was already processed (status: {$existingEvent->status}).");
            return response('Event already processed', 200);
        }

        // 3. Log webhook event start
        $webhookEvent = WebhookEvent::create([
            'provider' => 'central_payment',
            'event_id' => $eventId,
            'event_type' => $eventType,
            'payload' => $payload,
            'status' => 'processing',
        ]);

        try {
            // 4. Process event logic
            $integrationService->processWebhook($payload);

            // 5. Mark as completed
            $webhookEvent->update([
                'status' => 'completed',
            ]);

            return response('OK', 200);
        } catch (\Throwable $e) {
            Log::error("Error processing Central Payment Webhook {$eventId}: " . $e->getMessage(), [
                'exception' => $e
            ]);

            // 6. Mark as failed
            $webhookEvent->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return response('Processing error', 500);
        }
    }
}
