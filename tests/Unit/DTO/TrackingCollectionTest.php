<?php

declare(strict_types=1);

namespace AfterShip\Tests\Unit\DTO;

use AfterShip\DTO\TrackingCollection;
use AfterShip\DTO\TrackingData;
use PHPUnit\Framework\TestCase;

final class TrackingCollectionTest extends TestCase
{
    public function test_it_creates_collection_from_array(): void
    {
        $data = [
            'count' => 2,
            'page' => 1,
            'limit' => 10,
            'trackings' => [
                [
                    'id' => 'track-1',
                    'tracking_number' => 'TN001',
                    'slug' => 'dhl',
                    'tag' => 'InTransit',
                    'subtag' => 'InTransit_001',
                ],
                [
                    'id' => 'track-2',
                    'tracking_number' => 'TN002',
                    'slug' => 'fedex',
                    'tag' => 'Delivered',
                    'subtag' => 'Delivered_001',
                ],
            ],
        ];

        $collection = TrackingCollection::fromArray($data);

        $this->assertSame(2, $collection->count);
        $this->assertSame(1, $collection->page);
        $this->assertSame(10, $collection->limit);
        $this->assertCount(2, $collection->items);
        $this->assertInstanceOf(TrackingData::class, $collection->items[0]);
        $this->assertSame('TN001', $collection->items[0]->trackingNumber);
        $this->assertSame('TN002', $collection->items[1]->trackingNumber);
    }

    public function test_it_creates_empty_collection(): void
    {
        $collection = TrackingCollection::fromArray([]);

        $this->assertSame(0, $collection->count);
        $this->assertEmpty($collection->items);
        $this->assertNull($collection->page);
    }
}
