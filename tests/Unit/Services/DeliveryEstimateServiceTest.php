<?php

declare(strict_types=1);

namespace AfterShip\Tests\Unit\Services;

use AfterShip\Contracts\DriverInterface;
use AfterShip\DTO\DeliveryEstimateData;
use AfterShip\Services\DeliveryEstimateService;
use AfterShip\Support\ResponseMapper;
use PHPUnit\Framework\TestCase;

final class DeliveryEstimateServiceTest extends TestCase
{
    private DeliveryEstimateService $service;
    private DriverInterface $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->driver = $this->createMock(DriverInterface::class);
        $this->service = new DeliveryEstimateService($this->driver, new ResponseMapper());
    }

    public function test_it_estimates_delivery_date(): void
    {
        $data = [
            'slug' => 'dhl',
            'service_type_name' => 'Express',
            'origin_address' => 'New York',
            'destination_address' => 'London',
        ];

        $this->driver->expects($this->once())
            ->method('estimateDeliveryDate')
            ->with($data)
            ->willReturn([
                'data' => [
                    'estimated_delivery_date' => [
                        'slug' => 'dhl',
                        'service_type_name' => 'Express',
                        'origin_address' => 'New York',
                        'destination_address' => 'London',
                        'estimated_delivery_date' => '2024-01-15',
                        'estimated_delivery_date_min' => '2024-01-14',
                        'estimated_delivery_date_max' => '2024-01-16',
                        'confidence_score' => 90,
                    ],
                ],
            ]);

        $result = $this->service->estimate($data);

        $this->assertInstanceOf(DeliveryEstimateData::class, $result);
        $this->assertSame('dhl', $result->slug);
        $this->assertSame('Express', $result->serviceTypeName);
        $this->assertSame('2024-01-15', $result->estimatedDeliveryDate);
        $this->assertSame(90, $result->confidenceScore);
    }
}
