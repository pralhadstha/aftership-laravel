<?php

declare(strict_types=1);

namespace AfterShip\Contracts;

use AfterShip\Services\TrackingService;
use AfterShip\Services\CourierService;
use AfterShip\Services\DeliveryEstimateService;

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
