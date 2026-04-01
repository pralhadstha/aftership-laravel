<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Tests\Unit\Support;

use OmniCargo\Aftership\Laravel\DTO\CourierData;
use OmniCargo\Aftership\Laravel\DTO\DeliveryEstimateData;
use OmniCargo\Aftership\Laravel\DTO\TrackingCollection;
use OmniCargo\Aftership\Laravel\DTO\TrackingData;
use OmniCargo\Aftership\Laravel\Support\ResponseMapper;
use PHPUnit\Framework\TestCase;

final class ResponseMapperTest extends TestCase
{
    private ResponseMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = new ResponseMapper();
    }

    public function test_it_maps_tracking_response(): void
    {
        $response = [
            'data' => [
                'tracking' => [
                    'id' => 'track-1',
                    'tracking_number' => 'TN123',
                    'slug' => 'dhl',
                    'tag' => 'InTransit',
                    'subtag' => 'InTransit_001',
                ],
            ],
        ];

        $result = $this->mapper->toTracking($response);

        $this->assertInstanceOf(TrackingData::class, $result);
        $this->assertSame('track-1', $result->id);
        $this->assertSame('TN123', $result->trackingNumber);
    }

    public function test_it_maps_tracking_collection_response(): void
    {
        $response = [
            'data' => [
                'count' => 2,
                'page' => 1,
                'limit' => 10,
                'trackings' => [
                    [
                        'id' => 'track-1',
                        'tracking_number' => 'TN001',
                        'slug' => 'dhl',
                        'tag' => 'InTransit',
                        'subtag' => 'InTransit_001',
                    ],
                    [
                        'id' => 'track-2',
                        'tracking_number' => 'TN002',
                        'slug' => 'fedex',
                        'tag' => 'Delivered',
                        'subtag' => 'Delivered_001',
                    ],
                ],
            ],
        ];

        $result = $this->mapper->toTrackingCollection($response);

        $this->assertInstanceOf(TrackingCollection::class, $result);
        $this->assertSame(2, $result->count);
        $this->assertCount(2, $result->items);
    }

    public function test_it_maps_couriers_response(): void
    {
        $response = [
            'data' => [
                'couriers' => [
                    ['slug' => 'dhl', 'name' => 'DHL'],
                    ['slug' => 'fedex', 'name' => 'FedEx'],
                ],
            ],
        ];

        $result = $this->mapper->toCouriers($response);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(CourierData::class, $result[0]);
    }

    public function test_it_maps_single_courier_response(): void
    {
        $response = [
            'data' => [
                'courier' => [
                    'slug' => 'dhl',
                    'name' => 'DHL',
                    'phone' => '+1-800-225-5345',
                ],
            ],
        ];

        $result = $this->mapper->toCourier($response);

        $this->assertInstanceOf(CourierData::class, $result);
        $this->assertSame('dhl', $result->slug);
    }

    public function test_it_maps_detected_couriers_response(): void
    {
        $response = [
            'data' => [
                'couriers' => [
                    ['slug' => 'dhl', 'name' => 'DHL'],
                ],
            ],
        ];

        $result = $this->mapper->toDetectedCouriers($response);

        $this->assertCount(1, $result);
        $this->assertSame('dhl', $result[0]->slug);
    }

    public function test_it_maps_delivery_estimate_response(): void
    {
        $response = [
            'data' => [
                'estimated_delivery_date' => [
                    'slug' => 'dhl',
                    'service_type_name' => 'Express',
                    'estimated_delivery_date' => '2024-01-15',
                    'confidence_score' => 85,
                ],
            ],
        ];

        $result = $this->mapper->toDeliveryEstimate($response);

        $this->assertInstanceOf(DeliveryEstimateData::class, $result);
        $this->assertSame('dhl', $result->slug);
        $this->assertSame(85, $result->confidenceScore);
    }
}
