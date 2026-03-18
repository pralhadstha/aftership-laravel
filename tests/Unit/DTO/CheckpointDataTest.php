<?php

declare(strict_types=1);

namespace AfterShip\Tests\Unit\DTO;

use AfterShip\DTO\CheckpointData;
use PHPUnit\Framework\TestCase;

final class CheckpointDataTest extends TestCase
{
    public function test_it_creates_checkpoint_from_array(): void
    {
        $data = [
            'tag' => 'InTransit',
            'subtag' => 'InTransit_001',
            'message' => 'Package in transit',
            'location' => 'Distribution Center',
            'city' => 'New York',
            'state' => 'NY',
            'zip' => '10001',
            'country_iso3' => 'USA',
            'checkpoint_time' => '2024-01-10T14:30:00+00:00',
            'slug' => 'dhl',
        ];

        $checkpoint = CheckpointData::fromArray($data);

        $this->assertSame('InTransit', $checkpoint->tag);
        $this->assertSame('InTransit_001', $checkpoint->subtag);
        $this->assertSame('Package in transit', $checkpoint->message);
        $this->assertSame('Distribution Center', $checkpoint->location);
        $this->assertSame('New York', $checkpoint->city);
        $this->assertSame('NY', $checkpoint->state);
        $this->assertSame('10001', $checkpoint->zip);
        $this->assertSame('USA', $checkpoint->countryIso3);
        $this->assertSame('dhl', $checkpoint->slug);
    }

    public function test_it_creates_checkpoint_with_defaults(): void
    {
        $checkpoint = CheckpointData::fromArray([]);

        $this->assertSame('', $checkpoint->tag);
        $this->assertSame('', $checkpoint->subtag);
        $this->assertNull($checkpoint->message);
        $this->assertNull($checkpoint->city);
    }

    public function test_it_converts_to_array(): void
    {
        $checkpoint = new CheckpointData(
            tag: 'Delivered',
            subtag: 'Delivered_001',
            message: 'Delivered',
            city: 'London',
        );

        $result = $checkpoint->toArray();

        $this->assertSame('Delivered', $result['tag']);
        $this->assertSame('Delivered_001', $result['subtag']);
        $this->assertSame('Delivered', $result['message']);
        $this->assertSame('London', $result['city']);
    }
}
