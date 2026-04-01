<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Tests\Unit\Services;

use OmniCargo\Aftership\Laravel\Contracts\DriverInterface;
use OmniCargo\Aftership\Laravel\DTO\CourierData;
use OmniCargo\Aftership\Laravel\Services\CourierService;
use OmniCargo\Aftership\Laravel\Support\ResponseMapper;
use PHPUnit\Framework\TestCase;

final class CourierServiceTest extends TestCase
{
    private CourierService $service;
    private DriverInterface $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->driver = $this->createMock(DriverInterface::class);
        $this->service = new CourierService($this->driver, new ResponseMapper());
    }

    public function test_it_lists_couriers(): void
    {
        $this->driver->expects($this->once())
            ->method('listCouriers')
            ->willReturn([
                'data' => [
                    'couriers' => [
                        ['slug' => 'dhl', 'name' => 'DHL'],
                        ['slug' => 'fedex', 'name' => 'FedEx'],
                    ],
                ],
            ]);

        $result = $this->service->list();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(CourierData::class, $result[0]);
        $this->assertSame('dhl', $result[0]->slug);
        $this->assertSame('fedex', $result[1]->slug);
    }

    public function test_it_detects_courier(): void
    {
        $data = ['tracking_number' => 'TN123'];

        $this->driver->expects($this->once())
            ->method('detectCourier')
            ->with($data)
            ->willReturn([
                'data' => [
                    'couriers' => [
                        ['slug' => 'dhl', 'name' => 'DHL'],
                    ],
                ],
            ]);

        $result = $this->service->detect($data);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(CourierData::class, $result[0]);
        $this->assertSame('dhl', $result[0]->slug);
    }

    public function test_it_gets_courier(): void
    {
        $this->driver->expects($this->once())
            ->method('getCourier')
            ->with('dhl')
            ->willReturn([
                'data' => [
                    'courier' => [
                        'slug' => 'dhl',
                        'name' => 'DHL',
                        'phone' => '+1-800-225-5345',
                    ],
                ],
            ]);

        $result = $this->service->get('dhl');

        $this->assertInstanceOf(CourierData::class, $result);
        $this->assertSame('dhl', $result->slug);
        $this->assertSame('DHL', $result->name);
    }
}
