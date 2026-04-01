<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Contracts;

use OmniCargo\Aftership\Laravel\Services\TrackingService;
use OmniCargo\Aftership\Laravel\Services\CourierService;
use OmniCargo\Aftership\Laravel\Services\DeliveryEstimateService;

interface ClientInterface
{
    /**
     * Get the tracking service.
     */
    public function tracking(): TrackingService;

    /**
     * Get the courier service.
     */
    public function courier(): CourierService;

    /**
     * Get the delivery estimate service.
     */
    public function deliveryEstimate(): DeliveryEstimateService;

    /**
     * Get the underlying driver.
     */
    public function driver(): DriverInterface;
}
