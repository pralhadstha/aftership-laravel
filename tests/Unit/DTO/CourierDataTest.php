<?php

declare(strict_types=1);

namespace AfterShip\Tests\Unit\DTO;

use AfterShip\DTO\CourierData;
use PHPUnit\Framework\TestCase;

final class CourierDataTest extends TestCase
{
    public function test_it_creates_courier_from_array(): void
    {
        $data = [
            'slug' => 'dhl',
            'name' => 'DHL',
            'phone' => '+1-800-225-5345',
            'other_name' => 'DHL Express',
            'web_url' => 'https://www.dhl.com',
            'required_fields' => ['tracking_number'],
            'optional_fields' => ['order_number'],
        ];

        $courier = CourierData::fromArray($data);

        $this->assertSame('dhl', $courier->slug);
        $this->assertSame('DHL', $courier->name);
        $this->assertSame('+1-800-225-5345', $courier->phone);
        $this->assertSame('DHL Express', $courier->otherName);
        $this->assertSame('https://www.dhl.com', $courier->webUrl);
        $this->assertSame(['tracking_number'], $courier->requiredFields);
        $this->assertSame(['order_number'], $courier->optionalFields);
    }

    public function test_it_creates_courier_with_defaults(): void
    {
        $courier = CourierData::fromArray([]);

        $this->assertSame('', $courier->slug);
        $this->assertSame('', $courier->name);
        $this->assertNull($courier->phone);
        $this->assertEmpty($courier->requiredFields);
    }

    public function test_it_converts_to_array(): void
    {
        $courier = new CourierData(
            slug: 'fedex',
            name: 'FedEx',
            phone: '+1-800-463-3339',
        );

        $result = $courier->toArray();

        $this->assertSame('fedex', $result['slug']);
        $this->assertSame('FedEx', $result['name']);
        $this->assertSame('+1-800-463-3339', $result['phone']);
    }
}
