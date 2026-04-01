<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Services;

use OmniCargo\Aftership\Laravel\Contracts\DriverInterface;
use OmniCargo\Aftership\Laravel\DTO\TrackingCollection;
use OmniCargo\Aftership\Laravel\DTO\TrackingData;
use OmniCargo\Aftership\Laravel\Support\ResponseMapper;

final class TrackingService
{
    public function __construct(
        private readonly DriverInterface $driver,
        private readonly ResponseMapper $mapper,
    ) {}

    /**
     * Create a new tracking.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): TrackingData
    {
        $response = $this->driver->createTracking($data);

        return $this->mapper->toTracking($response);
    }

    /**
     * Get a tracking by ID.
     */
    public function get(string $id): TrackingData
    {
        $response = $this->driver->getTracking($id);

        return $this->mapper->toTracking($response);
    }

    /**
     * List trackings with optional query parameters.
     *
     * @param array<string, mixed> $query
     */
    public function list(array $query = []): TrackingCollection
    {
        $response = $this->driver->listTrackings($query);

        return $this->mapper->toTrackingCollection($response);
    }

    /**
     * Update a tracking by ID.
     *
     * @param array<string, mixed> $data
     */
    public function update(string $id, array $data): TrackingData
    {
        $response = $this->driver->updateTracking($id, $data);

        return $this->mapper->toTracking($response);
    }

    /**
     * Delete a tracking by ID.
     */
    public function delete(string $id): TrackingData
    {
        $response = $this->driver->deleteTracking($id);

        return $this->mapper->toTracking($response);
    }

    /**
     * Mark a tracking as completed.
     */
    public function markCompleted(string $id, string $reason = 'DELIVERED'): TrackingData
    {
        $response = $this->driver->markTrackingCompleted($id, $reason);

        return $this->mapper->toTracking($response);
    }
}
