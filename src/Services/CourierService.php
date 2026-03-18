<?php

declare(strict_types=1);

namespace AfterShip\Services;

use AfterShip\Contracts\DriverInterface;
use AfterShip\DTO\CourierData;
use AfterShip\Support\ResponseMapper;

final class CourierService
{
    public function __construct(
        private readonly DriverInterface $driver,
        private readonly ResponseMapper $mapper,
    ) {}

    /**
     * List all available couriers.
     *
     * @return array<int, CourierData>
     */
    public function list(): array
    {
        $response = $this->driver->listCouriers();

        return $this->mapper->toCouriers($response);
    }

    /**
     * Detect courier from tracking data.
     *
     * @param array<string, mixed> $data
     * @return array<int, CourierData>
     */
    public function detect(array $data): array
    {
        $response = $this->driver->detectCourier($data);

        return $this->mapper->toDetectedCouriers($response);
    }

    /**
     * Get courier details by slug.
     */
    public function get(string $slug): CourierData
    {
        $response = $this->driver->getCourier($slug);

        return $this->mapper->toCourier($response);
    }
}
