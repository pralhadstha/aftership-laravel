<?php

declare(strict_types=1);

namespace AfterShip\DTO;

final class DeliveryEstimateData
{
    public function __construct(
        public readonly string $slug,
        public readonly string $serviceTypeName,
        public readonly ?string $originAddress = null,
        public readonly ?string $destinationAddress = null,
        public readonly ?string $pickupTime = null,
        public readonly ?string $estimatedDeliveryDate = null,
        public readonly ?string $estimatedDeliveryDateMin = null,
        public readonly ?string $estimatedDeliveryDateMax = null,
        public readonly ?int $confidenceScore = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            slug: $data['slug'] ?? '',
            serviceTypeName: $data['service_type_name'] ?? '',
            originAddress: $data['origin_address'] ?? null,
            destinationAddress: $data['destination_address'] ?? null,
            pickupTime: $data['pickup_time'] ?? null,
            estimatedDeliveryDate: $data['estimated_delivery_date'] ?? null,
            estimatedDeliveryDateMin: $data['estimated_delivery_date_min'] ?? null,
            estimatedDeliveryDateMax: $data['estimated_delivery_date_max'] ?? null,
            confidenceScore: isset($data['confidence_score']) ? (int) $data['confidence_score'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'service_type_name' => $this->serviceTypeName,
            'origin_address' => $this->originAddress,
            'destination_address' => $this->destinationAddress,
            'pickup_time' => $this->pickupTime,
            'estimated_delivery_date' => $this->estimatedDeliveryDate,
            'estimated_delivery_date_min' => $this->estimatedDeliveryDateMin,
            'estimated_delivery_date_max' => $this->estimatedDeliveryDateMax,
            'confidence_score' => $this->confidenceScore,
        ];
    }
}
