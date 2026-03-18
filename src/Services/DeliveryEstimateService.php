<?php

declare(strict_types=1);

namespace AfterShip\Services;

use AfterShip\Contracts\DriverInterface;
use AfterShip\DTO\DeliveryEstimateData;
use AfterShip\Support\ResponseMapper;

final class DeliveryEstimateService
{
    public function __construct(
        private readonly DriverInterface $driver,
        private readonly ResponseMapper $mapper,
    ) {}

    /**
     * Get estimated delivery date.
     *
     * @param array<string, mixed> $data
     */
    public function estimate(array $data): DeliveryEstimateData
    {
        $response = $this->driver->estimateDeliveryDate($data);

        return $this->mapper->toDeliveryEstimate($response);
    }
}
