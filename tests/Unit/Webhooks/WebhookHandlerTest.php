<?php

declare(strict_types=1);

namespace AfterShip\Tests\Unit\Webhooks;

use AfterShip\Exceptions\AfterShipException;
use AfterShip\Webhooks\WebhookHandler;
use PHPUnit\Framework\TestCase;

final class WebhookHandlerTest extends TestCase
{
    public function test_it_handles_valid_payload(): void
    {
        $handler = new WebhookHandler();

        $payload = json_encode([
            'event' => 'tracking.updated',
            'msg' => [
                'id' => 'track-1',
                'tracking_number' => 'TN123',
                'slug' => 'dhl',
                'tag' => 'InTransit',
            ],
        ]);

        $result = $handler->handle($payload);

        $this->assertSame('tracking.updated', $result['event']);
        $this->assertSame('TN123', $result['msg']['tracking_number']);
    }

    public function test_it_verifies_valid_signature(): void
    {
        $secret = 'test-secret';
        $handler = new WebhookHandler($secret);

        $payload = json_encode(['event' => 'tracking.updated']);
        $signature = hash_hmac('sha256', $payload, $secret);

        $result = $handler->handle($payload, $signature);

        $this->assertSame('tracking.updated', $result['event']);
    }

    public function test_it_rejects_invalid_signature(): void
    {
        $handler = new WebhookHandler('test-secret');

        $payload = json_encode(['event' => 'tracking.updated']);

        $this->expectException(AfterShipException::class);
        $this->expectExceptionMessage('Invalid webhook signature');

        $handler->handle($payload, 'invalid-signature');
    }

    public function test_it_rejects_invalid_json(): void
    {
        $handler = new WebhookHandler();

        $this->expectException(AfterShipException::class);
        $this->expectExceptionMessage('Invalid webhook payload');

        $handler->handle('not-valid-json');
    }

    public function test_it_gets_event_name(): void
    {
        $handler = new WebhookHandler();

        $this->assertSame('tracking.updated', $handler->getEventName(['event' => 'tracking.updated']));
        $this->assertSame('unknown', $handler->getEventName([]));
    }

    public function test_it_gets_tracking_data(): void
    {
        $handler = new WebhookHandler();

        $data = [
            'msg' => [
                'id' => 'track-1',
                'tracking_number' => 'TN123',
            ],
        ];

        $result = $handler->getTrackingData($data);

        $this->assertSame('TN123', $result['tracking_number']);
    }

    public function test_it_skips_verification_without_secret(): void
    {
        $handler = new WebhookHandler('');

        $payload = json_encode(['event' => 'tracking.updated']);

        $result = $handler->handle($payload, 'any-signature');

        $this->assertSame('tracking.updated', $result['event']);
    }

    public function test_it_skips_verification_without_signature(): void
    {
        $handler = new WebhookHandler('my-secret');

        $payload = json_encode(['event' => 'tracking.delivered']);

        $result = $handler->handle($payload);

        $this->assertSame('tracking.delivered', $result['event']);
    }
}
