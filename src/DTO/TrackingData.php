<?php

declare(strict_types=1);

namespace AfterShip\DTO;

final class TrackingData
{
    /**
     * @param array<int, CheckpointData> $checkpoints
     * @param array<string, mixed> $customFields
     */
    public function __construct(
        public readonly string $id,
        public readonly string $trackingNumber,
        public readonly string $slug,
        public readonly string $tag,
        public readonly string $subtag,
        public readonly ?string $title = null,
        public readonly ?string $orderNumber = null,
        public readonly ?string $orderIdPath = null,
        public readonly ?string $shipmentType = null,
        public readonly ?string $originCountry = null,
        public readonly ?string $destinationCountry = null,
        public readonly ?string $expectedDelivery = null,
        public readonly ?string $signedBy = null,
        public readonly ?string $source = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
        public readonly array $checkpoints = [],
        public readonly array $customFields = [],
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $checkpoints = [];
        if (isset($data['checkpoints']) && is_array($data['checkpoints'])) {
            $checkpoints = array_map(
                fn (array $cp) => CheckpointData::fromArray($cp),
                $data['checkpoints']
            );
        }

        return new self(
            id: $data['id'] ?? '',
            trackingNumber: $data['tracking_number'] ?? '',
            slug: $data['slug'] ?? '',
            tag: $data['tag'] ?? '',
            subtag: $data['subtag'] ?? '',
            title: $data['title'] ?? null,
            orderNumber: $data['order_number'] ?? null,
            orderIdPath: $data['order_id_path'] ?? null,
            shipmentType: $data['shipment_type'] ?? null,
            originCountry: $data['origin_country_iso3'] ?? null,
            destinationCountry: $data['destination_country_iso3'] ?? null,
            expectedDelivery: $data['expected_delivery'] ?? null,
            signedBy: $data['signed_by'] ?? null,
            source: $data['source'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            checkpoints: $checkpoints,
            customFields: $data['custom_fields'] ?? [],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tracking_number' => $this->trackingNumber,
            'slug' => $this->slug,
            'tag' => $this->tag,
            'subtag' => $this->subtag,
            'title' => $this->title,
            'order_number' => $this->orderNumber,
            'order_id_path' => $this->orderIdPath,
            'shipment_type' => $this->shipmentType,
            'origin_country_iso3' => $this->originCountry,
            'destination_country_iso3' => $this->destinationCountry,
            'expected_delivery' => $this->expectedDelivery,
            'signed_by' => $this->signedBy,
            'source' => $this->source,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'checkpoints' => array_map(fn (CheckpointData $cp) => $cp->toArray(), $this->checkpoints),
            'custom_fields' => $this->customFields,
        ];
    }
}
