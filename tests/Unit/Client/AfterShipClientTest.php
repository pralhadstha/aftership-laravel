<?php

declare(strict_types=1);

namespace AfterShip\Tests\Unit\Client;

use AfterShip\Client\AfterShipClient;
use AfterShip\Contracts\DriverInterface;
use AfterShip\Services\CourierService;
use AfterShip\Services\DeliveryEstimateService;
use AfterShip\Services\TrackingService;
use PHPUnit\Framework\TestCase;

final class AfterShipClientTest extends TestCase
{
    private AfterShipClient $client;
    private DriverInterface $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->driver = $this->createMock(DriverInterface::class);
        $this->client = new AfterShipClient($this->driver);
    }

    public function test_it_returns_tracking_service(): void
    {
        $service = $this->client->tracking();

        $this->assertInstanceOf(TrackingService::class, $service);
    }

    public function test_it_returns_courier_service(): void
    {
        $service = $this->client->courier();

        $this->assertInstanceOf(CourierService::class, $service);
    }

    public function test_it_returns_delivery_estimate_service(): void
    {
        $service = $this->client->deliveryEstimate();

        $this->assertInstanceOf(DeliveryEstimateService::class, $service);
    }

    public function test_it_returns_driver(): void
    {
        $driver = $this->client->driver();

        $this->assertSame($this->driver, $driver);
    }

    public function test_it_caches_service_instances(): void
    {
        $tracking1 = $this->client->tracking();
        $tracking2 = $this->client->tracking();

        $this->assertSame($tracking1, $tracking2);

        $courier1 = $this->client->courier();
        $courier2 = $this->client->courier();

        $this->assertSame($courier1, $courier2);

        $estimate1 = $this->client->deliveryEstimate();
        $estimate2 = $this->client->deliveryEstimate();

        $this->assertSame($estimate1, $estimate2);
    }
}
