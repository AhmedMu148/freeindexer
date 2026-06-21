<?php

namespace App\Support;

use Carbon\Carbon;

final class PaymentCycleIdentity
{
    public function __construct(
        public string $subscriptionHash,
        public ?int $billingCycleCount = null,
        public ?Carbon $periodStart = null,
        public ?Carbon $periodEnd = null,
        public ?float $amount = null,
        public ?string $currency = null,
        public ?Carbon $paidAt = null,
        public ?string $centralPaymentTransactionId = null,
        public ?string $transactionHash = null,
        public ?string $providerTransactionId = null,
        public ?string $providerPaymentId = null,
        public ?string $providerInvoiceId = null,
        public ?string $providerSubscriptionId = null,
        public ?string $lastPaymentId = null,
        public ?Carbon $lastPaymentTime = null,
        public ?string $providerEventId = null,
        public ?string $providerEventType = null,
        public ?string $chargeSource = null,
        public ?string $renewalSource = null,
        public ?string $merchantOrderId = null,
        public ?string $paymentProvider = null,
        public ?string $sourceEvent = null,
        public ?string $eventKey = null,
        public ?int $captchaaiUserId = null,
    ) {}

    public function primaryOrderKey(): ?string
    {
        return $this->billingPeriodKey()
            ?? $this->transactionScopedKey()
            ?? $this->fallbackCycleKey();
    }

    public function billingPeriodKey(): ?string
    {
        return self::billingPeriodKeyFor($this->subscriptionHash, $this->billingCycleCount, $this->periodStart);
    }

    /**
     * @return array<int, string>
     */
    public function billingPeriodAliases(): array
    {
        return self::uniqueStrings([
            $this->billingCycleCount !== null
                ? self::cycleCountKey($this->subscriptionHash, $this->billingCycleCount)
                : null,
            $this->periodStart instanceof Carbon
                ? self::periodStartKey($this->subscriptionHash, $this->periodStart)
                : null,
            $this->centralPaymentTransactionId !== null
                ? self::scopedIdentifierKey($this->subscriptionHash, 'cp_tx', $this->centralPaymentTransactionId)
                : null,
            $this->transactionHash !== null
                ? self::scopedIdentifierKey($this->subscriptionHash, 'tx_hash', $this->transactionHash)
                : null,
            $this->providerTransactionId !== null
                ? self::scopedIdentifierKey($this->subscriptionHash, 'provider_tx', $this->providerTransactionId)
                : null,
            $this->providerPaymentId !== null
                ? self::scopedIdentifierKey($this->subscriptionHash, 'provider_payment', $this->providerPaymentId)
                : null,
            $this->providerInvoiceId !== null
                ? self::scopedIdentifierKey($this->subscriptionHash, 'provider_invoice', $this->providerInvoiceId)
                : null,
            $this->lastPaymentId !== null
                ? self::scopedIdentifierKey($this->subscriptionHash, 'last_payment_id', $this->lastPaymentId)
                : null,
            $this->recurringPaymentEvidenceKey(),
            $this->fallbackCycleKey(),
        ]);
    }

    public function paymentReference(): ?string
    {
        return self::firstScalar([
            $this->centralPaymentTransactionId,
            $this->transactionHash,
            $this->providerTransactionId,
            $this->providerPaymentId,
            $this->providerInvoiceId,
            $this->merchantOrderId,
            $this->primaryOrderKey(),
        ]);
    }

    public function paymentHash(): ?string
    {
        return self::firstScalar([
            $this->transactionHash,
            $this->centralPaymentTransactionId,
            $this->providerTransactionId,
            $this->providerPaymentId,
            $this->providerInvoiceId,
            $this->primaryOrderKey(),
        ]);
    }

    /**
     * @return array<int, string>
     */
    public function transactionIdentityCandidates(): array
    {
        return self::uniqueStrings([
            $this->centralPaymentTransactionId,
            $this->transactionHash,
            $this->providerTransactionId,
            $this->providerPaymentId,
            $this->providerInvoiceId,
            $this->merchantOrderId,
            $this->primaryOrderKey(),
            ...$this->billingPeriodAliases(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    public function metadata(array $extra = []): array
    {
        $metadata = $extra;
        $identityMetadata = [
            'central_payment_transaction_id' => $this->centralPaymentTransactionId,
            'transaction_hash' => $this->transactionHash,
            'provider_transaction_id' => $this->providerTransactionId,
            'provider_payment_id' => $this->providerPaymentId,
            'provider_invoice_id' => $this->providerInvoiceId,
            'provider_subscription_id' => $this->providerSubscriptionId,
            'last_payment_id' => $this->lastPaymentId,
            'last_payment_time' => $this->lastPaymentTime?->toISOString(),
            'provider_event_id' => $this->providerEventId,
            'provider_event_type' => $this->providerEventType,
            'charge_source' => $this->chargeSource,
            'renewal_source' => $this->renewalSource,
            'payment_provider' => $this->paymentProvider,
            'subscription_hash' => $this->subscriptionHash,
            'central_payment_subscription_id' => $this->subscriptionHash,
            'billing_period_key' => $this->primaryOrderKey(),
            'billing_period_keys' => $this->billingPeriodAliases(),
            'current_period_start' => $this->periodStart?->toISOString(),
            'current_period_end' => $this->periodEnd?->toISOString(),
            'billing_cycle_count' => $this->billingCycleCount,
            'source_event' => $this->sourceEvent,
            'merchant_order_id' => $this->merchantOrderId,
            'captchaai_user_id' => $this->captchaaiUserId,
        ];

        foreach ($identityMetadata as $key => $value) {
            if ($value === null || $value === '' || $value === []) {
                $metadata[$key] ??= $value;

                continue;
            }

            $metadata[$key] = $value;
        }

        return $metadata;
    }

    public static function billingPeriodKeyFor(string $subscriptionHash, ?int $billingCycleCount, mixed $periodStart): ?string
    {
        if ($billingCycleCount !== null) {
            return self::cycleCountKey($subscriptionHash, $billingCycleCount);
        }

        if ($periodStart instanceof Carbon) {
            return self::periodStartKey($subscriptionHash, $periodStart);
        }

        return null;
    }

    /**
     * @param  array<int, mixed>  $values
     * @return array<int, string>
     */
    public static function uniqueStrings(array $values): array
    {
        $strings = [];

        foreach ($values as $value) {
            if (is_array($value)) {
                foreach (self::uniqueStrings($value) as $nestedValue) {
                    $strings[$nestedValue] = $nestedValue;
                }

                continue;
            }

            if (is_scalar($value) && trim((string) $value) !== '') {
                $string = trim((string) $value);
                $strings[strtolower($string)] = $string;
            }
        }

        return array_values($strings);
    }

    /**
     * @param  array<int, mixed>  $values
     */
    public static function firstScalar(array $values): ?string
    {
        foreach ($values as $value) {
            if (is_scalar($value) && trim((string) $value) !== '') {
                return trim((string) $value);
            }
        }

        return null;
    }

    private function transactionScopedKey(): ?string
    {
        $identifier = self::firstScalar([
            $this->centralPaymentTransactionId,
            $this->transactionHash,
            $this->providerTransactionId,
            $this->providerInvoiceId,
        ]);

        return $identifier !== null
            ? self::scopedIdentifierKey($this->subscriptionHash, 'tx', $identifier)
            : null;
    }

    private function fallbackCycleKey(): ?string
    {
        $periodSource = $this->periodStart ?? $this->paidAt;

        if (! $periodSource instanceof Carbon || $this->amount === null) {
            return null;
        }

        return hash('sha256', $this->subscriptionHash.'|fallback|'.$periodSource->toDateString().'|'.number_format($this->amount, 2, '.', ''));
    }

    private function recurringPaymentEvidenceKey(): ?string
    {
        if ($this->lastPaymentId === null && ! $this->lastPaymentTime instanceof Carbon) {
            return null;
        }

        $evidenceTime = $this->lastPaymentTime?->toISOString() ?? 'no-time';
        $evidenceAmount = $this->amount !== null
            ? number_format($this->amount, 2, '.', '')
            : 'unknown-amount';
        $evidenceCurrency = $this->currency !== null ? strtoupper($this->currency) : 'unknown-currency';

        return hash('sha256', implode('|', [
            $this->subscriptionHash,
            'recurring_payment_evidence',
            $this->providerSubscriptionId ?? 'no-provider-subscription',
            $this->lastPaymentId ?? 'no-payment-id',
            $evidenceTime,
            $evidenceAmount,
            $evidenceCurrency,
        ]));
    }

    private static function cycleCountKey(string $subscriptionHash, int $billingCycleCount): string
    {
        return hash('sha256', $subscriptionHash.'|cycle|'.$billingCycleCount);
    }

    private static function periodStartKey(string $subscriptionHash, Carbon $periodStart): string
    {
        return hash('sha256', $subscriptionHash.'|period|'.$periodStart->toISOString());
    }

    private static function scopedIdentifierKey(string $subscriptionHash, string $scope, string $identifier): string
    {
        return hash('sha256', $subscriptionHash.'|'.$scope.'|'.$identifier);
    }
}
