<?php

namespace App\Support\Payments;

class PaymentProviderResolver
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function fromMetadata(array $metadata, ?string $fallback = null): string
    {
        $preferredCandidates = [
            data_get($metadata, 'provider.name'),
            data_get($metadata, 'payment_provider'),
            data_get($metadata, 'gateway_name'),
            data_get($metadata, 'gateway.name'),
            data_get($metadata, 'gateway_data.provider.name'),
            data_get($metadata, 'gateway_data.gateway.name'),
            data_get($metadata, 'provider_metadata.provider'),
            data_get($metadata, 'provider_metadata.gateway'),
            data_get($metadata, 'provider_metadata.gateway_response.provider'),
            data_get($metadata, 'provider_metadata.gateway_response.gateway'),
            data_get($metadata, 'payment_method.method_slug'),
            data_get($metadata, 'payment_method'),
            data_get($metadata, 'gateway_data.metadata.provider'),
            data_get($metadata, 'gateway_data.metadata.gateway'),
        ];

        foreach ($preferredCandidates as $candidate) {
            $normalized = $this->normalize($candidate);

            if ($normalized !== null && ! $this->isGenericProvider($normalized)) {
                return $normalized;
            }
        }

        $secondaryCandidates = [
            data_get($metadata, 'provider.display_name'),
            data_get($metadata, 'gateway.display_name'),
            data_get($metadata, 'gateway_data.provider.display_name'),
            data_get($metadata, 'gateway_data.gateway.display_name'),
            data_get($metadata, 'payment_method_label'),
        ];

        foreach ($secondaryCandidates as $candidate) {
            $normalized = $this->normalize($candidate);

            if ($normalized !== null && ! $this->isGenericProvider($normalized)) {
                return $normalized;
            }
        }

        return $this->normalize($fallback) ?? 'central_payment';
    }

    private function normalize(mixed $value): ?string
    {
        if (! is_scalar($value)) {
            return null;
        }

        $normalized = strtolower(trim((string) $value));

        if ($normalized === '') {
            return null;
        }

        $normalized = (string) preg_replace('/[^a-z0-9]+/', '_', $normalized);
        $normalized = trim($normalized, '_');

        return $normalized !== '' ? $normalized : null;
    }

    private function isGenericProvider(string $provider): bool
    {
        return in_array($provider, [
            'central_payment',
            'payment_gateway',
            'gateway',
            'provider_missing',
            'unknown',
            'null',
            'none',
        ], true);
    }
}
