<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Webhooks;

use OmniCargo\Aftership\Laravel\Exceptions\AfterShipException;

final class WebhookHandler
{
    public function __construct(
        private readonly string $secret = '',
    ) {}

    /**
     * Verify and parse an incoming webhook payload.
     *
     * @return array<string, mixed>
     *
     * @throws AfterShipException
     */
    public function handle(string $payload, ?string $signature = null): array
    {
        if ($this->secret !== '' && $signature !== null) {
            $this->verifySignature($payload, $signature);
        }

        /** @var array<string, mixed>|null $data */
        $data = json_decode($payload, true);

        if ($data === null) {
            throw new AfterShipException('Invalid webhook payload: unable to decode JSON.');
        }

        return $data;
    }

    /**
     * Get the event name from a webhook payload.
     *
     * @param array<string, mixed> $payload
     */
    public function getEventName(array $payload): string
    {
        return $payload['event'] ?? 'unknown';
    }

    /**
     * Get the tracking data from a webhook payload.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function getTrackingData(array $payload): array
    {
        return $payload['msg'] ?? [];
    }

    /**
     * Verify the webhook signature.
     *
     * @throws AfterShipException
     */
    private function verifySignature(string $payload, string $signature): void
    {
        $computedSignature = hash_hmac('sha256', $payload, $this->secret);

        if (!hash_equals($computedSignature, $signature)) {
            throw new AfterShipException('Invalid webhook signature.');
        }
    }
}
