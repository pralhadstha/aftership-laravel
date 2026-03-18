<?php

declare(strict_types=1);

namespace AfterShip\Drivers;

use AfterShip\Contracts\DriverInterface;

final class FakeDriver implements DriverInterface
{
    /** @var array<int, array{method: string, args: array<mixed>}> */
    private array $calls = [];

    /** @var array<string, array<string, mixed>> */
    private array $responses = [];

    /**
     * Set a fake response for a method.
     *
     * @param array<string, mixed> $response
     */
    public function addResponse(string $method, array $response): self
    {
        $this->responses[$method] = $response;

        return $this;
    }

    /**
     * Get all recorded calls.
     *
     * @return array<int, array{method: string, args: array<mixed>}>
     */
    public function getCalls(): array
    {
        return $this->calls;
    }

    /**
     * Assert a method was called.
     */
    public function assertCalled(string $method): bool
    {
        foreach ($this->calls as $call) {
            if ($call['method'] === $method) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the number of times a method was called.
     */
    public function callCount(string $method): int
    {
        return count(array_filter(
            $this->calls,
            fn (array $call) => $call['method'] === $method,
        ));
    }

    /**
     * Reset all recorded calls and responses.
     */
    public function reset(): void
    {
        $this->calls = [];
        $this->responses = [];
    }

    public function createTracking(array $data): array
    {
        return $this->record('createTracking', [$data], [
            'data' => [
                'tracking' => array_merge([
                    'id' => 'fake-tracking-id',
                    'tracking_number' => $data['tracking_number'] ?? '123456',
                    'slug' => $data['slug'] ?? 'dhl',
                    'tag' => 'Pending',
                    'subtag' => 'Pending_001',
                    'created_at' => '2024-01-01T00:00:00+00:00',
                    'updated_at' => '2024-01-01T00:00:00+00:00',
                ], $data),
            ],
        ]);
    }

    public function getTracking(string $id): array
    {
        return $this->record('getTracking', [$id], [
            'data' => [
                'tracking' => [
                    'id' => $id,
                    'tracking_number' => '123456',
                    'slug' => 'dhl',
                    'tag' => 'InTransit',
                    'subtag' => 'InTransit_001',
                    'created_at' => '2024-01-01T00:00:00+00:00',
                    'updated_at' => '2024-01-01T00:00:00+00:00',
                ],
            ],
        ]);
    }

    public function listTrackings(array $query = []): array
    {
        return $this->record('listTrackings', [$query], [
            'data' => [
                'count' => 1,
                'trackings' => [
                    [
                        'id' => 'fake-tracking-id',
                        'tracking_number' => '123456',
                        'slug' => 'dhl',
                        'tag' => 'InTransit',
                        'subtag' => 'InTransit_001',
                    ],
                ],
            ],
        ]);
    }

    public function updateTracking(string $id, array $data): array
    {
        return $this->record('updateTracking', [$id, $data], [
            'data' => [
                'tracking' => array_merge([
                    'id' => $id,
                    'tracking_number' => '123456',
                    'slug' => 'dhl',
                    'tag' => 'InTransit',
                    'subtag' => 'InTransit_001',
                ], $data),
            ],
        ]);
    }

    public function deleteTracking(string $id): array
    {
        return $this->record('deleteTracking', [$id], [
            'data' => [
                'tracking' => [
                    'id' => $id,
                    'tracking_number' => '123456',
                    'slug' => 'dhl',
                    'tag' => 'Pending',
                    'subtag' => 'Pending_001',
                ],
            ],
        ]);
    }

    public function markTrackingCompleted(string $id, string $reason = 'DELIVERED'): array
    {
        return $this->record('markTrackingCompleted', [$id, $reason], [
            'data' => [
                'tracking' => [
                    'id' => $id,
                    'tracking_number' => '123456',
                    'slug' => 'dhl',
                    'tag' => 'Delivered',
                    'subtag' => 'Delivered_001',
                ],
            ],
        ]);
    }

    public function listCouriers(): array
    {
        return $this->record('listCouriers', [], [
            'data' => [
                'couriers' => [
                    [
                        'slug' => 'dhl',
                        'name' => 'DHL',
                        'phone' => '+1-800-225-5345',
                        'web_url' => 'https://www.dhl.com',
                        'required_fields' => [],
                        'optional_fields' => [],
                    ],
                    [
                        'slug' => 'fedex',
                        'name' => 'FedEx',
                        'phone' => '+1-800-463-3339',
                        'web_url' => 'https://www.fedex.com',
                        'required_fields' => [],
                        'optional_fields' => [],
                    ],
                ],
            ],
        ]);
    }

    public function detectCourier(array $data): array
    {
        return $this->record('detectCourier', [$data], [
            'data' => [
                'couriers' => [
                    [
                        'slug' => 'dhl',
                        'name' => 'DHL',
                    ],
                ],
            ],
        ]);
    }

    public function getCourier(string $slug): array
    {
        return $this->record('getCourier', [$slug], [
            'data' => [
                'courier' => [
                    'slug' => $slug,
                    'name' => strtoupper($slug),
                    'phone' => '+1-800-000-0000',
                    'web_url' => "https://www.{$slug}.com",
                    'required_fields' => [],
                    'optional_fields' => [],
                ],
            ],
        ]);
    }

    public function estimateDeliveryDate(array $data): array
    {
        return $this->record('estimateDeliveryDate', [$data], [
            'data' => [
                'estimated_delivery_date' => [
                    'slug' => $data['slug'] ?? 'dhl',
                    'service_type_name' => $data['service_type_name'] ?? 'Standard',
                    'origin_address' => $data['origin_address'] ?? null,
                    'destination_address' => $data['destination_address'] ?? null,
                    'estimated_delivery_date' => '2024-01-15',
                    'estimated_delivery_date_min' => '2024-01-14',
                    'estimated_delivery_date_max' => '2024-01-16',
                    'confidence_score' => 90,
                ],
            ],
        ]);
    }

    /**
     * @param array<mixed> $args
     * @param array<string, mixed> $defaultResponse
     * @return array<string, mixed>
     */
    private function record(string $method, array $args, array $defaultResponse): array
    {
        $this->calls[] = ['method' => $method, 'args' => $args];

        return $this->responses[$method] ?? $defaultResponse;
    }
}
