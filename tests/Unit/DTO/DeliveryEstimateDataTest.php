<?php

declare(strict_types=1);

namespace AfterShip\Tests\Unit\DTO;

use AfterShip\DTO\DeliveryEstimateData;
use PHPUnit\Framework\TestCase;

final class DeliveryEstimateDataTest extends TestCase
{
    public function test_it_creates_estimate_from_array(): void
    {
        $data = [
            'slug' => 'dhl',
            'service_type_name' => 'Express',
            'origin_address' => 'New York, USA',
            'destination_address' => 'London, UK',
            'pickup_time' => '2024-01-10',
            'estimated_delivery_date' => '2024-01-15',
            'estimated_delivery_date_min' => '2024-01-14',
            'estimated_delivery_date_max' => '2024-01-16',
            'confidence_score' => 85,
        ];

        $estimate = DeliveryEstimateData::fromArray($data);

        $this->assertSame('dhl', $estimate->slug);
        $this->assertSame('Express', $estimate->serviceTypeName);
        $this->assertSame('New York, USA', $estimate->originAddress);
        $this->assertSame('London, UK', $estimate->destinationAddress);
        $this->assertSame('2024-01-15', $estimate->estimatedDeliveryDate);
        $this->assertSame('2024-01-14', $estimate->estimatedDeliveryDateMin);
        $this->assertSame('2024-01-16', $estimate->estimatedDeliveryDateMax);
        $this->assertSame(85, $estimate->confidenceScore);
    }

    public function test_it_creates_estimate_with_defaults(): void
    {
        $estimate = DeliveryEstimateData::fromArray([]);

        $this->assertSame('', $estimate->slug);
        $this->assertSame('', $estimate->serviceTypeName);
        $this->assertNull($estimate->estimatedDeliveryDate);
        $this->assertNull($estimate->confidenceScore);
    }

    public function test_it_converts_to_array(): void
    {
        $estimate = new DeliveryEstimateData(
            slug: 'dhl',
            serviceTypeName: 'Standard',
            estimatedDeliveryDate: '2024-01-20',
            confidenceScore: 90,
        );

        $result = $estimate->toArray();

        $this->assertSame('dhl', $result['slug']);
        $this->assertSame('Standard', $result['service_type_name']);
        $this->assertSame('2024-01-20', $result['estimated_delivery_date']);
        $this->assertSame(90, $result['confidence_score']);
    }
}
