<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Tests\Feature;

use OmniCargo\Aftership\Laravel\DTO\CourierData;
use OmniCargo\Aftership\Laravel\DTO\DeliveryEstimateData;
use OmniCargo\Aftership\Laravel\DTO\TrackingCollection;
use OmniCargo\Aftership\Laravel\DTO\TrackingData;
use OmniCargo\Aftership\Laravel\Facades\AfterShip;
use OmniCargo\Aftership\Laravel\Services\CourierService;
use OmniCargo\Aftership\Laravel\Services\DeliveryEstimateService;
use OmniCargo\Aftership\Laravel\Services\TrackingService;
use OmniCargo\Aftership\Laravel\Tests\TestCase;

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
