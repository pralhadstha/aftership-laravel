<?php

declare(strict_types=1);

namespace AfterShip\Tests\Feature;

use AfterShip\DTO\CourierData;
use AfterShip\DTO\DeliveryEstimateData;
use AfterShip\DTO\TrackingCollection;
use AfterShip\DTO\TrackingData;
use AfterShip\Facades\AfterShip;
use AfterShip\Services\CourierService;
use AfterShip\Services\DeliveryEstimateService;
use AfterShip\Services\TrackingService;
use AfterShip\Tests\TestCase;

final class FacadeTest extends TestCase
{
    public function test_facade_resolves_tracking_service(): void
    {
        $service = AfterShip::tracking();

        $this->assertInstanceOf(TrackingService::class, $service);
    }

    public function test_facade_resolves_courier_service(): void
    {
        $service = AfterShip::courier();

        $this->assertInstanceOf(CourierService::class, $service);
    }

    public function test_facade_resolves_delivery_estimate_service(): void
    {
        $service = AfterShip::deliveryEstimate();

        $this->assertInstanceOf(DeliveryEstimateService::class, $service);
    }

    public function test_facade_can_create_tracking(): void
    {
        $result = AfterShip::tracking()->create([
            'tracking_number' => 'TN123456',
            'slug' => 'dhl',
        ]);

        $this->assertInstanceOf(TrackingData::class, $result);
        $this->assertSame('TN123456', $result->trackingNumber);
    }

    public function test_facade_can_list_couriers(): void
    {
        $result = AfterShip::courier()->list();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(CourierData::class, $result[0]);
    }

    public function test_facade_can_list_trackings(): void
    {
        $result = AfterShip::tracking()->list();

        $this->assertInstanceOf(TrackingCollection::class, $result);
        $this->assertGreaterThanOrEqual(0, $result->count);
    }

    public function test_facade_can_estimate_delivery(): void
    {
        $result = AfterShip::deliveryEstimate()->estimate([
            'slug' => 'dhl',
            'service_type_name' => 'Express',
        ]);

        $this->assertInstanceOf(DeliveryEstimateData::class, $result);
        $this->assertSame('dhl', $result->slug);
    }
}
