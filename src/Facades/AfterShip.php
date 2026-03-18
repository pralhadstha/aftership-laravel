<?php

declare(strict_types=1);

namespace AfterShip\Facades;

use AfterShip\Contracts\ClientInterface;
use AfterShip\Contracts\DriverInterface;
use AfterShip\Services\CourierService;
use AfterShip\Services\DeliveryEstimateService;
use AfterShip\Services\TrackingService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static TrackingService tracking()
 * @method static CourierService courier()
 * @method static DeliveryEstimateService deliveryEstimate()
 * @method static DriverInterface driver()
 *
 * @see \AfterShip\Client\AfterShipClient
 */
final class AfterShip extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ClientInterface::class;
    }
}
