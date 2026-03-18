<?php

declare(strict_types=1);

namespace AfterShip\Tests\Unit\Services;

use AfterShip\Contracts\DriverInterface;
use AfterShip\DTO\TrackingCollection;
use AfterShip\DTO\TrackingData;
use AfterShip\Services\TrackingService;
use AfterShip\Support\ResponseMapper;
use PHPUnit\Framework\TestCase;

final class TrackingServiceTest extends TestCase
{
    private TrackingService $service;
    private DriverInterface $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->driver = $this->createMock(DriverInterface::class);
        $this->service = new TrackingService($this->driver, new ResponseMapper());
    }

    public function test_it_creates_tracking(): void
    {
        $inputData = ['tracking_number' => 'TN123', 'slug' => 'dhl'];

        $this->driver->expects($this->once())
            ->method('createTracking')
            ->with($inputData)
            ->willReturn([
                'data' => [
                    'tracking' => [
                        'id' => 'new-id',
                        'tracking_number' => 'TN123',
                        'slug' => 'dhl',
                        'tag' => 'Pending',
                        'subtag' => 'Pending_001',
                    ],
                ],
            ]);

        $result = $this->service->create($inputData);

        $this->assertInstanceOf(TrackingData::class, $result);
        $this->assertSame('TN123', $result->trackingNumber);
        $this->assertSame('dhl', $result->slug);
        $this->assertSame('Pending', $result->tag);
    }

    public function test_it_gets_tracking(): void
    {
        $this->driver->expects($this->once())
            ->method('getTracking')
            ->with('track-id')
            ->willReturn([
                'data' => [
                    'tracking' => [
                        'id' => 'track-id',
                        'tracking_number' => 'TN123',
                        'slug' => 'dhl',
                        'tag' => 'InTransit',
                        'subtag' => 'InTransit_001',
                    ],
                ],
            ]);

        $result = $this->service->get('track-id');

        $this->assertInstanceOf(TrackingData::class, $result);
        $this->assertSame('track-id', $result->id);
    }

    public function test_it_lists_trackings(): void
    {
        $query = ['page' => 1, 'limit' => 10];

        $this->driver->expects($this->once())
            ->method('listTrackings')
            ->with($query)
            ->willReturn([
                'data' => [
                    'count' => 1,
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
                    ],
                ],
            ]);

        $result = $this->service->list($query);

        $this->assertInstanceOf(TrackingCollection::class, $result);
        $this->assertSame(1, $result->count);
        $this->assertCount(1, $result->items);
    }

    public function test_it_updates_tracking(): void
    {
        $updateData = ['title' => 'Updated Title'];

        $this->driver->expects($this->once())
            ->method('updateTracking')
            ->with('track-id', $updateData)
            ->willReturn([
                'data' => [
                    'tracking' => [
                        'id' => 'track-id',
                        'tracking_number' => 'TN123',
                        'slug' => 'dhl',
                        'tag' => 'InTransit',
                        'subtag' => 'InTransit_001',
                        'title' => 'Updated Title',
                    ],
                ],
            ]);

        $result = $this->service->update('track-id', $updateData);

        $this->assertInstanceOf(TrackingData::class, $result);
        $this->assertSame('Updated Title', $result->title);
    }

    public function test_it_deletes_tracking(): void
    {
        $this->driver->expects($this->once())
            ->method('deleteTracking')
            ->with('track-id')
            ->willReturn([
                'data' => [
                    'tracking' => [
                        'id' => 'track-id',
                        'tracking_number' => 'TN123',
                        'slug' => 'dhl',
                        'tag' => 'Pending',
                        'subtag' => 'Pending_001',
                    ],
                ],
            ]);

        $result = $this->service->delete('track-id');

        $this->assertInstanceOf(TrackingData::class, $result);
    }

    public function test_it_marks_tracking_completed(): void
    {
        $this->driver->expects($this->once())
            ->method('markTrackingCompleted')
            ->with('track-id', 'DELIVERED')
            ->willReturn([
                'data' => [
                    'tracking' => [
                        'id' => 'track-id',
                        'tracking_number' => 'TN123',
                        'slug' => 'dhl',
                        'tag' => 'Delivered',
                        'subtag' => 'Delivered_001',
                    ],
                ],
            ]);

        $result = $this->service->markCompleted('track-id');

        $this->assertInstanceOf(TrackingData::class, $result);
        $this->assertSame('Delivered', $result->tag);
    }
}
