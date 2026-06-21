<?php

namespace App\Support;

use Carbon\Carbon;

class SubscriptionBillingIdentityResolver
{
    /**
     * @param  array<string, mixed>  $subscriptionData
     * @param  array<string, mixed>  $data
     */
    public function fromSubscriptionRenewedPayload(
        string $subscriptionHash,
        array $subscriptionData,
        array $data,
        ?Carbon $eventTimestamp = null,
        ?Carbon $paidAt = null,
        ?float $amount = null,
        ?string $currency = null,
    ): PaymentCycleIdentity {
        $transaction = $this->arrayValue($subscriptionData['transaction'] ?? $data['transaction'] ?? []);
        $transactionMetadata = $this->arrayValue($transaction['metadata'] ?? []);
        $subscriptionMetadata = $this->arrayValue($subscriptionData['metadata'] ?? []);
        $webhookMetadata = $this->arrayValue($data['metadata'] ?? []);
        $providerMetadata = $this->arrayValue($subscriptionData['provider_metadata'] ?? $data['provider_metadata'] ?? []);
        $lastPayment = $this->arrayValue(data_get($providerMetadata, 'gateway_response.billing_info.last_payment', []));
        $gateway = $this->gatewayName($subscriptionData['gateway'] ?? $data['gateway'] ?? null)
            ?? $this->scalar($subscriptionData['payment_method'] ?? $data['payment_method'] ?? null);
        $providerPaymentId = $this->scalar(
            $transactionMetadata['provider_payment_id']
            ?? $subscriptionMetadata['provider_payment_id']
            ?? $subscriptionData['provider_payment_id']
            ?? $transaction['provider_payment_id']
            ?? $providerMetadata['payment_intent']
            ?? data_get($providerMetadata, 'gateway_response.payment_intent')
            ?? data_get($providerMetadata, 'gateway_response.provider_payment_id')
            ?? $data['provider_payment_id']
            ?? null
        );

        return new PaymentCycleIdentity(
            $subscriptionHash,
            $this->integer($subscriptionData['billing_cycle_count'] ?? $data['billing_cycle_count'] ?? $subscriptionMetadata['billing_cycle_count'] ?? $webhookMetadata['billing_cycle_count'] ?? null),
            $this->carbon($subscriptionData['current_period_start'] ?? $data['current_period_start'] ?? $subscriptionMetadata['current_period_start'] ?? $subscriptionMetadata['period_start'] ?? $webhookMetadata['current_period_start'] ?? $webhookMetadata['period_start'] ?? null),
            $this->carbon($subscriptionData['current_period_end'] ?? $data['current_period_end'] ?? $subscriptionMetadata['current_period_end'] ?? $subscriptionMetadata['period_end'] ?? $webhookMetadata['current_period_end'] ?? $webhookMetadata['period_end'] ?? null),
            $amount,
            $currency !== null ? strtoupper($currency) : $this->scalar($subscriptionData['currency'] ?? $data['currency'] ?? null),
            $paidAt ?? $eventTimestamp,
            $this->scalar(
                $subscriptionData['central_payment_transaction_id']
                ?? $subscriptionMetadata['central_payment_transaction_id']
                ?? $transaction['central_payment_transaction_id']
                ?? $transaction['id']
                ?? $data['central_payment_transaction_id']
                ?? null
            ),
            $this->scalar(
                $subscriptionData['transaction_hash']
                ?? $subscriptionMetadata['transaction_hash']
                ?? $transaction['transaction_hash']
                ?? $transaction['hash']
                ?? $data['transaction_hash']
                ?? null
            ),
            $this->scalar(
                $subscriptionData['provider_transaction_id']
                ?? $subscriptionMetadata['provider_transaction_id']
                ?? $transaction['provider_transaction_id']
                ?? $transactionMetadata['provider_transaction_id']
                ?? $providerPaymentId
                ?? $providerMetadata['charge']
                ?? $providerMetadata['provider_transaction_id']
                ?? data_get($providerMetadata, 'gateway_response.charge')
                ?? data_get($providerMetadata, 'gateway_response.provider_transaction_id')
                ?? data_get($subscriptionData, 'provider_metadata.provider_transaction_id')
                ?? data_get($transaction, 'provider_metadata.provider_transaction_id')
                ?? $data['provider_transaction_id']
                ?? null
            ),
            $providerPaymentId,
            $this->scalar(
                $subscriptionData['provider_invoice_id']
                ?? $subscriptionMetadata['provider_invoice_id']
                ?? $subscriptionMetadata['invoice_id']
                ?? $transaction['provider_invoice_id']
                ?? $transactionMetadata['provider_invoice_id']
                ?? $transactionMetadata['invoice_id']
                ?? data_get($subscriptionData, 'provider_metadata.provider_invoice_id')
                ?? data_get($transaction, 'provider_metadata.provider_invoice_id')
                ?? data_get($transaction, 'provider_metadata.gateway_response.invoice')
                ?? $data['provider_invoice_id']
                ?? null
            ),
            $this->scalar(
                $transactionMetadata['provider_subscription_id']
                ?? $subscriptionMetadata['provider_subscription_id']
                ?? $subscriptionData['provider_subscription_id']
                ?? $transaction['provider_subscription_id']
                ?? data_get($subscriptionData, 'provider_metadata.provider_subscription_id')
                ?? data_get($transaction, 'provider_metadata.provider_subscription_id')
                ?? $data['provider_subscription_id']
                ?? null
            ),
            $this->scalar(
                $subscriptionData['last_payment_id']
                ?? $subscriptionMetadata['last_payment_id']
                ?? $transactionMetadata['last_payment_id']
                ?? $webhookMetadata['last_payment_id']
                ?? $lastPayment['id']
                ?? null
            ),
            $this->carbon(
                $subscriptionData['last_payment_time']
                ?? $subscriptionMetadata['last_payment_time']
                ?? $transactionMetadata['last_payment_time']
                ?? $webhookMetadata['last_payment_time']
                ?? $lastPayment['time']
                ?? null
            ),
            $this->scalar($transactionMetadata['provider_event_id'] ?? $subscriptionMetadata['provider_event_id'] ?? $data['provider_event_id'] ?? null),
            $this->scalar($transactionMetadata['provider_event_type'] ?? $subscriptionMetadata['provider_event_type'] ?? $subscriptionData['provider_event_type'] ?? $data['provider_event_type'] ?? null),
            $this->scalar($transactionMetadata['charge_source'] ?? $subscriptionMetadata['charge_source'] ?? $subscriptionData['charge_source'] ?? $transaction['charge_source'] ?? $data['charge_source'] ?? null),
            $this->scalar($transactionMetadata['renewal_source'] ?? $subscriptionMetadata['renewal_source'] ?? $subscriptionData['renewal_source'] ?? $transaction['renewal_source'] ?? $data['renewal_source'] ?? null),
            $this->scalar($webhookMetadata['merchant_order_id'] ?? $subscriptionMetadata['merchant_order_id'] ?? $subscriptionData['merchant_order_id'] ?? $transactionMetadata['merchant_order_id'] ?? $transaction['merchant_order_id'] ?? $providerMetadata['merchant_order_id'] ?? data_get($providerMetadata, 'gateway_response.order_id') ?? data_get($providerMetadata, 'gateway_response.paypro_ipn.order_id') ?? $data['merchant_order_id'] ?? null),
            $gateway,
            'subscription.renewed',
            null,
            $this->integer($webhookMetadata['captchaai_user_id'] ?? $transactionMetadata['captchaai_user_id'] ?? $subscriptionMetadata['captchaai_user_id'] ?? $subscriptionMetadata['user_id'] ?? data_get($data, 'metadata.user_id')),
        );
    }

    /**
     * @param  array<string, mixed>  $transaction
     * @param  array<string, mixed>  $eventContext
     */
    public function fromTransactionCompletedPayload(
        string $subscriptionHash,
        array $transaction,
        array $eventContext = [],
        ?string $gateway = null,
        ?Carbon $paidAt = null,
    ): PaymentCycleIdentity {
        $subscription = $this->arrayValue($transaction['subscription'] ?? []);
        $providerMetadata = $this->arrayValue($transaction['provider_metadata'] ?? []);
        $transactionMetadata = $this->arrayValue($transaction['metadata'] ?? []);
        $webhookMetadata = $this->arrayValue($transaction['webhook_metadata'] ?? []);
        $subscriptionMetadata = $this->arrayValue($subscription['metadata'] ?? []);
        $lastPayment = $this->arrayValue(data_get($providerMetadata, 'gateway_response.billing_info.last_payment', []));
        $providerPaymentId = $this->scalar(
            $transactionMetadata['provider_payment_id']
            ?? $transaction['provider_payment_id']
            ?? $providerMetadata['provider_payment_id']
            ?? data_get($providerMetadata, 'gateway_response.provider_payment_id')
            ?? null
        );

        return new PaymentCycleIdentity(
            $subscriptionHash,
            $this->integer($transaction['billing_cycle_count'] ?? $subscription['billing_cycle_count'] ?? $transactionMetadata['billing_cycle_count'] ?? $webhookMetadata['billing_cycle_count'] ?? $subscriptionMetadata['billing_cycle_count'] ?? null),
            $this->carbon($transaction['current_period_start'] ?? $subscription['current_period_start'] ?? $transactionMetadata['current_period_start'] ?? $transactionMetadata['period_start'] ?? $webhookMetadata['current_period_start'] ?? $webhookMetadata['period_start'] ?? $subscriptionMetadata['current_period_start'] ?? $subscriptionMetadata['period_start'] ?? null),
            $this->carbon($transaction['current_period_end'] ?? $subscription['current_period_end'] ?? $transactionMetadata['current_period_end'] ?? $transactionMetadata['period_end'] ?? $webhookMetadata['current_period_end'] ?? $webhookMetadata['period_end'] ?? $subscriptionMetadata['current_period_end'] ?? $subscriptionMetadata['period_end'] ?? null),
            is_numeric($transaction['amount'] ?? null) ? (float) $transaction['amount'] : null,
            $this->scalar($transaction['currency'] ?? null),
            $paidAt,
            $this->scalar($transaction['central_payment_transaction_id'] ?? $transaction['id'] ?? null),
            $this->scalar($transaction['transaction_hash'] ?? $transaction['hash'] ?? null),
            $this->scalar(
                $transaction['provider_transaction_id']
                ?? $transactionMetadata['provider_transaction_id']
                ?? $providerPaymentId
                ?? $providerMetadata['provider_transaction_id']
                ?? data_get($providerMetadata, 'gateway_response.provider_transaction_id')
                ?? null
            ),
            $providerPaymentId,
            $this->scalar(
                $transaction['provider_invoice_id']
                ?? $transactionMetadata['provider_invoice_id']
                ?? $providerMetadata['provider_invoice_id']
                ?? data_get($providerMetadata, 'gateway_response.invoice')
                ?? data_get($providerMetadata, 'gateway_response.provider_invoice_id')
                ?? null
            ),
            $this->scalar(
                $transactionMetadata['provider_subscription_id']
                ?? $transaction['provider_subscription_id']
                ?? $providerMetadata['provider_subscription_id']
                ?? data_get($providerMetadata, 'gateway_response.provider_subscription_id')
                ?? null
            ),
            $this->scalar(
                $transaction['last_payment_id']
                ?? $transactionMetadata['last_payment_id']
                ?? $webhookMetadata['last_payment_id']
                ?? $subscriptionMetadata['last_payment_id']
                ?? $lastPayment['id']
                ?? null
            ),
            $this->carbon(
                $transaction['last_payment_time']
                ?? $transactionMetadata['last_payment_time']
                ?? $webhookMetadata['last_payment_time']
                ?? $subscriptionMetadata['last_payment_time']
                ?? $lastPayment['time']
                ?? null
            ),
            $this->scalar($transactionMetadata['provider_event_id'] ?? $eventContext['provider_event_key'] ?? null),
            $this->scalar($transactionMetadata['provider_event_type'] ?? $eventContext['event_type'] ?? null),
            $this->scalar($transactionMetadata['charge_source'] ?? $transaction['charge_source'] ?? null),
            $this->scalar($transactionMetadata['renewal_source'] ?? $transaction['renewal_source'] ?? null),
            $this->scalar($webhookMetadata['merchant_order_id'] ?? $transactionMetadata['merchant_order_id'] ?? $transaction['merchant_order_id'] ?? $providerMetadata['merchant_order_id'] ?? data_get($subscription, 'metadata.merchant_order_id') ?? null),
            $gateway ?? $this->gatewayName($transaction['gateway'] ?? null),
            'transaction.completed',
            $this->scalar($eventContext['provider_event_key'] ?? null),
            $this->integer($webhookMetadata['captchaai_user_id'] ?? $transactionMetadata['captchaai_user_id'] ?? data_get($subscription, 'metadata.captchaai_user_id') ?? data_get($subscription, 'metadata.user_id') ?? data_get($transaction, 'metadata.user_id')),
        );
    }

    /**
     * @param  array<string, mixed>  $existing
     * @param  array<string, mixed>  $incoming
     * @return array<string, mixed>
     */
    public function mergeMetadata(array $existing, array $incoming): array
    {
        foreach ($incoming as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (is_array($value) && $value === []) {
                continue;
            }

            if (is_array($value) && is_array($existing[$key] ?? null)) {
                $existing[$key] = $this->mergeMetadata($existing[$key], $value);

                continue;
            }

            $existing[$key] = $value;
        }

        return $existing;
    }

    public function billingPeriodKey(string $subscriptionHash, ?int $billingCycleCount, mixed $currentPeriodStart): ?string
    {
        return PaymentCycleIdentity::billingPeriodKeyFor($subscriptionHash, $billingCycleCount, $this->carbon($currentPeriodStart) ?? $currentPeriodStart);
    }

    private function scalar(mixed $value): ?string
    {
        if (! is_scalar($value) || trim((string) $value) === '') {
            return null;
        }

        return trim((string) $value);
    }

    private function integer(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private function carbon(mixed $value): ?Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if (! is_scalar($value) || trim((string) $value) === '') {
            return null;
        }

        try {
            return Carbon::parse((string) $value);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function arrayValue(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    private function gatewayName(mixed $gateway): ?string
    {
        if (is_array($gateway)) {
            return $this->scalar($gateway['name'] ?? $gateway['display_name'] ?? null);
        }

        return $this->scalar($gateway);
    }
}
