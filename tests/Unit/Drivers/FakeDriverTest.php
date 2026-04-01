<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Tests\Unit\Drivers;

use OmniCargo\Aftership\Laravel\Drivers\FakeDriver;
use PHPUnit\Framework\TestCase;

final class FakeDriverTest extends TestCase
{
    private FakeDriver $driver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->driver = new FakeDriver();
    }

    public function test_it_creates_tracking(): void
    {
        $result = $this->driver->createTracking([
            'tracking_number' => 'TN123',
            'slug' => 'dhl',
        ]);

        $this->assertArrayHasKey('data', $result);
        $this->assertSame('TN123', $result['data']['tracking']['tracking_number']);
        $this->assertTrue($this->driver->assertCalled('createTracking'));
    }

    public function test_it_gets_tracking(): void
    {
        $result = $this->driver->getTracking('track-id');

        $this->assertArrayHasKey('data', $result);
        $this->assertSame('track-id', $result['data']['tracking']['id']);
    }

    public function test_it_lists_trackings(): void
    {
        $result = $this->driver->listTrackings();

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('trackings', $result['data']);
    }

    public function test_it_updates_tracking(): void
    {
        $result = $this->driver->updateTracking('track-id', ['title' => 'Updated']);

        $this->assertArrayHasKey('data', $result);
        $this->assertSame('track-id', $result['data']['tracking']['id']);
    }

    public function test_it_deletes_tracking(): void
    {
        $result = $this->driver->deleteTracking('track-id');

        $this->assertArrayHasKey('data', $result);
    }

    public function test_it_marks_tracking_completed(): void
    {
        $result = $this->driver->markTrackingCompleted('track-id');

        $this->assertArrayHasKey('data', $result);
        $this->assertSame('Delivered', $result['data']['tracking']['tag']);
    }

    public function test_it_lists_couriers(): void
    {
        $result = $this->driver->listCouriers();

        $this->assertArrayHasKey('data', $result);
        $this->assertCount(2, $result['data']['couriers']);
    }

    public function test_it_detects_courier(): void
    {
        $result = $this->driver->detectCourier(['tracking_number' => 'TN123']);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('couriers', $result['data']);
    }

    public function test_it_gets_courier(): void
    {
        $result = $this->driver->getCourier('dhl');

        $this->assertArrayHasKey('data', $result);
        $this->assertSame('dhl', $result['data']['courier']['slug']);
    }

    public function test_it_estimates_delivery_date(): void
    {
        $result = $this->driver->estimateDeliveryDate([
            'slug' => 'dhl',
            'service_type_name' => 'Express',
        ]);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('estimated_delivery_date', $result['data']);
    }

    public function test_it_records_calls(): void
    {
        $this->driver->createTracking(['tracking_number' => 'TN1']);
        $this->driver->getTracking('id-1');
        $this->driver->createTracking(['tracking_number' => 'TN2']);

        $this->assertSame(2, $this->driver->callCount('createTracking'));
        $this->assertSame(1, $this->driver->callCount('getTracking'));
        $this->assertCount(3, $this->driver->getCalls());
    }

    public function test_it_uses_custom_responses(): void
    {
        $this->driver->addResponse('getTracking', [
            'data' => [
                'tracking' => [
                    'id' => 'custom-id',
                    'tracking_number' => 'CUSTOM',
                    'slug' => 'ups',
                    'tag' => 'Delivered',
                    'subtag' => 'Delivered_001',
                ],
            ],
        ]);

        $result = $this->driver->getTracking('any-id');

        $this->assertSame('custom-id', $result['data']['tracking']['id']);
        $this->assertSame('CUSTOM', $result['data']['tracking']['tracking_number']);
    }

    public function test_it_resets_state(): void
    {
        $this->driver->createTracking(['tracking_number' => 'TN1']);
        $this->driver->addResponse('getTracking', ['custom' => true]);
        $this->driver->reset();

        $this->assertEmpty($this->driver->getCalls());
        $this->assertFalse($this->driver->assertCalled('createTracking'));
    }
}
