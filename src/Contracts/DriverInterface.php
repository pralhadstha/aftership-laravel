<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Contracts;

interface DriverInterface
{
    /**
     * Create a tracking.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createTracking(array $data): array;

    /**
     * Get a tracking by ID.
     *
     * @return array<string, mixed>
     */
    public function getTracking(string $id): array;

    /**
     * List trackings with optional query parameters.
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function listTrackings(array $query = []): array;

    /**
     * Update a tracking by ID.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateTracking(string $id, array $data): array;

    /**
     * Delete a tracking by ID.
     *
     * @return array<string, mixed>
     */
    public function deleteTracking(string $id): array;

    /**
     * Mark a tracking as completed.
     *
     * @return array<string, mixed>
     */
    public function markTrackingCompleted(string $id, string $reason = 'DELIVERED'): array;

    /**
     * List all couriers.
     *
     * @return array<string, mixed>
     */
    public function listCouriers(): array;

    /**
     * Detect courier from tracking number.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function detectCourier(array $data): array;

    /**
     * Get courier details by slug.
     *
     * @return array<string, mixed>
     */
    public function getCourier(string $slug): array;

    /**
     * Get estimated delivery date.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function estimateDeliveryDate(array $data): array;
}
