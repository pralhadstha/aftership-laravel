<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Support;

use OmniCargo\Aftership\Laravel\DTO\CourierData;
use OmniCargo\Aftership\Laravel\DTO\DeliveryEstimateData;
use OmniCargo\Aftership\Laravel\DTO\TrackingCollection;
use OmniCargo\Aftership\Laravel\DTO\TrackingData;

final class ResponseMapper
{
    /**
     * @param array<string, mixed> $response
     */
    public function toTracking(array $response): TrackingData
    {
        $data = $response['data']['tracking'] ?? $response['tracking'] ?? $response;

        return TrackingData::fromArray($data);
    }

    /**
     * @param array<string, mixed> $response
     */
    public function toTrackingCollection(array $response): TrackingCollection
    {
        $data = $response['data'] ?? $response;

        return TrackingCollection::fromArray($data);
    }

    /**
     * @param array<string, mixed> $response
     * @return array<int, CourierData>
     */
    public function toCouriers(array $response): array
    {
        $couriers = $response['data']['couriers'] ?? $response['couriers'] ?? [];

        return array_map(
            fn (array $courier) => CourierData::fromArray($courier),
            $couriers,
        );
    }

    /**
     * @param array<string, mixed> $response
     */
    public function toCourier(array $response): CourierData
    {
        $data = $response['data']['courier'] ?? $response['courier'] ?? $response;

        return CourierData::fromArray($data);
    }

    /**
     * @param array<string, mixed> $response
     * @return array<int, CourierData>
     */
    public function toDetectedCouriers(array $response): array
    {
        $couriers = $response['data']['couriers'] ?? $response['couriers'] ?? [];

        return array_map(
            fn (array $courier) => CourierData::fromArray($courier),
            $couriers,
        );
    }

    /**
     * @param array<string, mixed> $response
     */
    public function toDeliveryEstimate(array $response): DeliveryEstimateData
    {
        $data = $response['data']['estimated_delivery_date'] ?? $response['estimated_delivery_date'] ?? $response;

        return DeliveryEstimateData::fromArray($data);
    }
}
