<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Facades;

use OmniCargo\Aftership\Laravel\Contracts\ClientInterface;
use OmniCargo\Aftership\Laravel\Contracts\DriverInterface;
use OmniCargo\Aftership\Laravel\Services\CourierService;
use OmniCargo\Aftership\Laravel\Services\DeliveryEstimateService;
use OmniCargo\Aftership\Laravel\Services\TrackingService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static TrackingService tracking()
 * @method static CourierService courier()
 * @method static DeliveryEstimateService deliveryEstimate()
 * @method static DriverInterface driver()
 *
 * @see \OmniCargo\Aftership\Laravel\Client\AfterShipClient
 */
final class AfterShip extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ClientInterface::class;
    }
}
