<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Tests\Unit;

use OmniCargo\Aftership\Laravel\AfterShipManager;
use OmniCargo\Aftership\Laravel\Client\AfterShipClient;
use OmniCargo\Aftership\Laravel\Contracts\ClientInterface;
use OmniCargo\Aftership\Laravel\Drivers\FakeDriver;
use OmniCargo\Aftership\Laravel\Drivers\SdkDriver;
use OmniCargo\Aftership\Laravel\Exceptions\InvalidConfigurationException;
use PHPUnit\Framework\TestCase;

final class AfterShipManagerTest extends TestCase
{
    public function test_it_creates_fake_client(): void
    {
        $manager = new AfterShipManager([
            'api_key' => 'test-key',
            'driver' => 'fake',
        ]);

        $client = $manager->client();

        $this->assertInstanceOf(ClientInterface::class, $client);
        $this->assertInstanceOf(AfterShipClient::class, $client);
        $this->assertInstanceOf(FakeDriver::class, $client->driver());
    }

    public function test_it_creates_sdk_client(): void
    {
        $manager = new AfterShipManager([
            'api_key' => 'test-key',
            'driver' => 'sdk',
            'base_url' => 'https://api.aftership.com',
            'timeout' => 30,
        ]);

        $client = $manager->client();

        $this->assertInstanceOf(ClientInterface::class, $client);
        $this->assertInstanceOf(SdkDriver::class, $client->driver());
    }

    public function test_it_throws_on_invalid_driver(): void
    {
        $manager = new AfterShipManager([
            'api_key' => 'test-key',
            'driver' => 'invalid',
        ]);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Invalid AfterShip driver [invalid]');

        $manager->client();
    }

    public function test_it_throws_on_missing_api_key(): void
    {
        $manager = new AfterShipManager([
            'api_key' => '',
            'driver' => 'sdk',
        ]);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Missing required AfterShip configuration: [api_key]');

        $manager->client();
    }

    public function test_it_returns_default_driver(): void
    {
        $manager = new AfterShipManager(['driver' => 'sdk']);

        $this->assertSame('sdk', $manager->getDefaultDriver());
    }

    public function test_it_defaults_to_sdk_driver(): void
    {
        $manager = new AfterShipManager([]);

        $this->assertSame('sdk', $manager->getDefaultDriver());
    }

    public function test_it_caches_client_instances(): void
    {
        $manager = new AfterShipManager([
            'api_key' => 'test-key',
            'driver' => 'fake',
        ]);

        $client1 = $manager->client();
        $client2 = $manager->client();

        $this->assertSame($client1, $client2);
    }

    public function test_it_creates_different_clients_for_different_drivers(): void
    {
        $manager = new AfterShipManager([
            'api_key' => 'test-key',
            'driver' => 'fake',
        ]);

        $fakeClient = $manager->client('fake');
        $sdkClient = $manager->client('sdk');

        $this->assertNotSame($fakeClient, $sdkClient);
    }

    public function test_http_driver_throws_without_http_factory(): void
    {
        $manager = new AfterShipManager([
            'api_key' => 'test-key',
            'driver' => 'http',
        ]);

        $this->expectException(InvalidConfigurationException::class);

        $manager->client();
    }
}
