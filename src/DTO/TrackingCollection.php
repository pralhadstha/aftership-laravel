<?php

declare(strict_types=1);

namespace AfterShip\DTO;

final class TrackingCollection
{
    /**
     * @param array<int, TrackingData> $items
     */
    public function __construct(
        public readonly array $items,
        public readonly int $count,
        public readonly ?int $page = null,
        public readonly ?int $limit = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $trackings = $data['trackings'] ?? [];

        $items = array_map(
            fn (array $tracking) => TrackingData::fromArray($tracking),
            $trackings
        );

        return new self(
            items: $items,
            count: (int) ($data['count'] ?? count($items)),
            page: isset($data['page']) ? (int) $data['page'] : null,
            limit: isset($data['limit']) ? (int) $data['limit'] : null,
        );
    }
}
