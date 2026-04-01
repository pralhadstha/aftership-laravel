<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Tests\Unit\DTO;

use OmniCargo\Aftership\Laravel\DTO\CheckpointData;
use OmniCargo\Aftership\Laravel\DTO\TrackingData;
use PHPUnit\Framework\TestCase;

final class TrackingDataTest extends TestCase
{
    public function test_it_creates_tracking_data_from_array(): void
    {
        $data = [
            'id' => 'tracking-123',
            'tracking_number' => 'TN123456',
            'slug' => 'dhl',
            'tag' => 'InTransit',
            'subtag' => 'InTransit_001',
            'title' => 'Test Tracking',
            'order_number' => 'ORD-001',
            'origin_country_iso3' => 'USA',
            'destination_country_iso3' => 'GBR',
            'expected_delivery' => '2024-01-15',
            'signed_by' => 'John Doe',
            'source' => 'api',
            'created_at' => '2024-01-01T00:00:00+00:00',
            'updated_at' => '2024-01-02T00:00:00+00:00',
            'checkpoints' => [
                [
                    'tag' => 'InTransit',
                    'subtag' => 'InTransit_001',
                    'message' => 'Package in transit',
                    'city' => 'New York',
                    'country_iso3' => 'USA',
                ],
            ],
            'custom_fields' => ['key' => 'value'],
        ];

        $tracking = TrackingData::fromArray($data);

        $this->assertSame('tracking-123', $tracking->id);
        $this->assertSame('TN123456', $tracking->trackingNumber);
        $this->assertSame('dhl', $tracking->slug);
        $this->assertSame('InTransit', $tracking->tag);
        $this->assertSame('InTransit_001', $tracking->subtag);
        $this->assertSame('Test Tracking', $tracking->title);
        $this->assertSame('ORD-001', $tracking->orderNumber);
        $this->assertSame('USA', $tracking->originCountry);
        $this->assertSame('GBR', $tracking->destinationCountry);
        $this->assertSame('2024-01-15', $tracking->expectedDelivery);
        $this->assertSame('John Doe', $tracking->signedBy);
        $this->assertSame('api', $tracking->source);
        $this->assertCount(1, $tracking->checkpoints);
        $this->assertInstanceOf(CheckpointData::class, $tracking->checkpoints[0]);
        $this->assertSame(['key' => 'value'], $tracking->customFields);
    }

    public function test_it_creates_tracking_data_with_defaults(): void
    {
        $tracking = TrackingData::fromArray([]);

        $this->assertSame('', $tracking->id);
        $this->assertSame('', $tracking->trackingNumber);
        $this->assertSame('', $tracking->slug);
        $this->assertNull($tracking->title);
        $this->assertEmpty($tracking->checkpoints);
        $this->assertEmpty($tracking->customFields);
    }

    public function test_it_converts_to_array(): void
    {
        $data = [
            'id' => 'tracking-123',
            'tracking_number' => 'TN123456',
            'slug' => 'dhl',
            'tag' => 'InTransit',
            'subtag' => 'InTransit_001',
            'title' => null,
            'order_number' => null,
            'order_id_path' => null,
            'shipment_type' => null,
            'origin_country_iso3' => null,
            'destination_country_iso3' => null,
            'expected_delivery' => null,
            'signed_by' => null,
            'source' => null,
            'created_at' => null,
            'updated_at' => null,
        ];

        $tracking = TrackingData::fromArray($data);
        $result = $tracking->toArray();

        $this->assertSame('tracking-123', $result['id']);
        $this->assertSame('TN123456', $result['tracking_number']);
        $this->assertSame('dhl', $result['slug']);
        $this->assertSame('InTransit', $result['tag']);
        $this->assertIsArray($result['checkpoints']);
        $this->assertIsArray($result['custom_fields']);
    }

    public function test_tracking_data_is_immutable(): void
    {
        $tracking = new TrackingData(
            id: 'test',
            trackingNumber: 'TN123',
            slug: 'dhl',
            tag: 'Pending',
            subtag: 'Pending_001',
        );

        $reflection = new \ReflectionClass($tracking);

        foreach ($reflection->getProperties() as $property) {
            $this->assertTrue($property->isReadOnly(), "Property {$property->getName()} should be readonly");
        }
    }
}
