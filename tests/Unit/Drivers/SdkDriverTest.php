<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Tests\Unit\Drivers;

use OmniCargo\Aftership\Laravel\Drivers\SdkDriver;
use OmniCargo\Aftership\Laravel\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;
use Tracking\API\Courier as CourierApi;
use Tracking\API\EstimatedDeliveryDate as EstimatedDeliveryDateApi;
use Tracking\API\Tracking as TrackingApi;
use Tracking\Client;
use Tracking\Exception\AfterShipError;
use Tracking\Model\Courier;
use Tracking\Model\CreateTrackingResponse;
use Tracking\Model\DeleteTrackingByIdResponse;
use Tracking\Model\DetectCourierResponse;
use Tracking\Model\DetectCourierResponseData;
use Tracking\Model\EstimatedDeliveryDateResponse;
use Tracking\Model\GetCouriersResponse;
use Tracking\Model\GetCouriersResponseData;
use Tracking\Model\GetTrackingByIdResponse;
use Tracking\Model\GetTrackingsResponse;
use Tracking\Model\GetTrackingsResponseData;
use Tracking\Model\GetTrackingsResponseDataPagination;
use Tracking\Model\MarkTrackingCompletedByIdResponse;
use Tracking\Model\PredictResponse;
use Tracking\Model\Tag;
use Tracking\Model\Tracking;
use Tracking\Model\UpdateTrackingByIdResponse;

final class SdkDriverTest extends TestCase
{
    private SdkDriver $driver;
    private Client $client;
    private TrackingApi $trackingApi;
    private CourierApi $courierApi;
    private EstimatedDeliveryDateApi $eddApi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trackingApi = $this->createMock(TrackingApi::class);
        $this->courierApi = $this->createMock(CourierApi::class);
        $this->eddApi = $this->createMock(EstimatedDeliveryDateApi::class);

        $this->client = $this->createMock(Client::class);
        $this->client->tracking = $this->trackingApi;
        $this->client->courier = $this->courierApi;
        $this->client->estimated_delivery_date = $this->eddApi;

        $this->driver = new SdkDriver(
            apiKey: 'test-api-key',
            baseUrl: 'https://api.aftership.com',
            timeout: 30,
        );

        // Inject mock client via reflection
        $ref = new \ReflectionClass($this->driver);
        $prop = $ref->getProperty('client');
        $prop->setValue($this->driver, $this->client);
    }

    public function test_it_creates_tracking(): void
    {
        $tracking = $this->makeTracking('track-1', 'TN123', 'dhl');

        $response = $this->createMock(CreateTrackingResponse::class);
        $response->method('getData')->willReturn($tracking);

        $this->trackingApi->expects($this->once())
            ->method('createTracking')
            ->willReturn($response);

        $result = $this->driver->createTracking([
            'tracking_number' => 'TN123',
            'slug' => 'dhl',
        ]);

        $this->assertSame('track-1', $result['data']['tracking']['id']);
        $this->assertSame('TN123', $result['data']['tracking']['tracking_number']);
    }

    public function test_it_gets_tracking_by_id(): void
    {
        $tracking = $this->makeTracking('track-1', 'TN123', 'dhl');

        $response = $this->createMock(GetTrackingByIdResponse::class);
        $response->method('getData')->willReturn($tracking);

        $this->trackingApi->expects($this->once())
            ->method('getTrackingById')
            ->with('track-1')
            ->willReturn($response);

        $result = $this->driver->getTracking('track-1');

        $this->assertSame('track-1', $result['data']['tracking']['id']);
    }

    public function test_it_lists_trackings(): void
    {
        $tracking = $this->makeTracking('track-1', 'TN123', 'dhl');

        $pagination = new GetTrackingsResponseDataPagination();
        $pagination->total = 1;

        $data = new GetTrackingsResponseData();
        $data->trackings = [$tracking];
        $data->pagination = $pagination;

        $response = $this->createMock(GetTrackingsResponse::class);
        $response->method('getData')->willReturn($data);

        $this->trackingApi->expects($this->once())
            ->method('getTrackings')
            ->willReturn($response);

        $result = $this->driver->listTrackings(['limit' => '10']);

        $this->assertSame(1, $result['data']['count']);
        $this->assertCount(1, $result['data']['trackings']);
    }

    public function test_it_updates_tracking(): void
    {
        $tracking = $this->makeTracking('track-1', 'TN123', 'dhl');
        $tracking->title = 'Updated';

        $response = $this->createMock(UpdateTrackingByIdResponse::class);
        $response->method('getData')->willReturn($tracking);

        $this->trackingApi->expects($this->once())
            ->method('updateTrackingById')
            ->willReturn($response);

        $result = $this->driver->updateTracking('track-1', ['title' => 'Updated']);

        $this->assertSame('Updated', $result['data']['tracking']['title']);
    }

    public function test_it_deletes_tracking(): void
    {
        $tracking = $this->makeTracking('track-1', 'TN123', 'dhl');

        $response = $this->createMock(DeleteTrackingByIdResponse::class);
        $response->method('getData')->willReturn($tracking);

        $this->trackingApi->expects($this->once())
            ->method('deleteTrackingById')
            ->with('track-1')
            ->willReturn($response);

        $result = $this->driver->deleteTracking('track-1');

        $this->assertSame('track-1', $result['data']['tracking']['id']);
    }

    public function test_it_marks_tracking_completed(): void
    {
        $tracking = $this->makeTracking('track-1', 'TN123', 'dhl');
        $tracking->tag = Tag::DELIVERED;

        $response = $this->createMock(MarkTrackingCompletedByIdResponse::class);
        $response->method('getData')->willReturn($tracking);

        $this->trackingApi->expects($this->once())
            ->method('markTrackingCompletedById')
            ->willReturn($response);

        $result = $this->driver->markTrackingCompleted('track-1', 'DELIVERED');

        $this->assertSame('track-1', $result['data']['tracking']['id']);
    }

    public function test_it_lists_couriers(): void
    {
        $courier = new Courier();
        $courier->slug = 'dhl';
        $courier->name = 'DHL';

        $data = new GetCouriersResponseData();
        $data->couriers = [$courier];

        $response = $this->createMock(GetCouriersResponse::class);
        $response->method('getData')->willReturn($data);

        $this->courierApi->expects($this->once())
            ->method('getCouriers')
            ->willReturn($response);

        $result = $this->driver->listCouriers();

        $this->assertCount(1, $result['data']['couriers']);
        $this->assertSame('dhl', $result['data']['couriers'][0]['slug']);
    }

    public function test_it_detects_courier(): void
    {
        $courier = new Courier();
        $courier->slug = 'dhl';
        $courier->name = 'DHL';

        $data = new DetectCourierResponseData();
        $data->couriers = [$courier];

        $response = $this->createMock(DetectCourierResponse::class);
        $response->method('getData')->willReturn($data);

        $this->courierApi->expects($this->once())
            ->method('detectCourier')
            ->willReturn($response);

        $result = $this->driver->detectCourier(['tracking_number' => 'TN123']);

        $this->assertCount(1, $result['data']['couriers']);
    }

    public function test_it_gets_courier_by_slug(): void
    {
        $courier = new Courier();
        $courier->slug = 'dhl';
        $courier->name = 'DHL';

        $data = new GetCouriersResponseData();
        $data->couriers = [$courier];

        $response = $this->createMock(GetCouriersResponse::class);
        $response->method('getData')->willReturn($data);

        $this->courierApi->expects($this->once())
            ->method('getCouriers')
            ->willReturn($response);

        $result = $this->driver->getCourier('dhl');

        $this->assertSame('dhl', $result['data']['courier']['slug']);
    }

    public function test_it_estimates_delivery_date(): void
    {
        $estimate = new EstimatedDeliveryDateResponse();
        $estimate->slug = 'dhl';
        $estimate->estimated_delivery_date = '2024-01-15';

        $response = $this->createMock(PredictResponse::class);
        $response->method('getData')->willReturn($estimate);

        $this->eddApi->expects($this->once())
            ->method('predict')
            ->willReturn($response);

        $result = $this->driver->estimateDeliveryDate([
            'slug' => 'dhl',
            'service_type_name' => 'Express',
        ]);

        $this->assertSame('dhl', $result['data']['estimated_delivery_date']['slug']);
    }

    public function test_it_throws_api_exception_on_sdk_error(): void
    {
        $this->trackingApi->method('getTrackingById')
            ->willThrowException(new AfterShipError('Not Found', 4004, 404));

        $this->expectException(ApiException::class);

        $this->driver->getTracking('nonexistent');
    }

    private function makeTracking(string $id, string $trackingNumber, string $slug): Tracking
    {
        $tracking = new Tracking();
        $tracking->id = $id;
        $tracking->tracking_number = $trackingNumber;
        $tracking->slug = $slug;
        $tracking->tag = Tag::PENDING;
        $tracking->subtag = 'Pending_001';

        return $tracking;
    }
}
