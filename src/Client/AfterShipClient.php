<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Client;

use OmniCargo\Aftership\Laravel\Contracts\ClientInterface;
use OmniCargo\Aftership\Laravel\Contracts\DriverInterface;
use OmniCargo\Aftership\Laravel\Services\CourierService;
use OmniCargo\Aftership\Laravel\Services\DeliveryEstimateService;
use OmniCargo\Aftership\Laravel\Services\TrackingService;
use OmniCargo\Aftership\Laravel\Support\ResponseMapper;

final class AfterShipClient implements ClientInterface
{
    private ?TrackingService $trackingService = null;
    private ?CourierService $courierService = null;
    private ?DeliveryEstimateService $deliveryEstimateService = null;
    private readonly ResponseMapper $mapper;

    public function __construct(
        private readonly DriverInterface $driver,
    ) {
        $this->mapper = new ResponseMapper();
    }

    public function tracking(): TrackingService
    {
        if ($this->trackingService === null) {
            $this->trackingService = new TrackingService($this->driver, $this->mapper);
        }

        return $this->trackingService;
    }

    public function courier(): CourierService
    {
        if ($this->courierService === null) {
            $this->courierService = new CourierService($this->driver, $this->mapper);
        }

        return $this->courierService;
    }

    public function deliveryEstimate(): DeliveryEstimateService
    {
        if ($this->deliveryEstimateService === null) {
            $this->deliveryEstimateService = new DeliveryEstimateService($this->driver, $this->mapper);
        }

        return $this->deliveryEstimateService;
    }

    public function driver(): DriverInterface
    {
        return $this->driver;
    }
}
