<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Services;

use OmniCargo\Aftership\Laravel\Contracts\DriverInterface;
use OmniCargo\Aftership\Laravel\DTO\DeliveryEstimateData;
use OmniCargo\Aftership\Laravel\Support\ResponseMapper;

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
