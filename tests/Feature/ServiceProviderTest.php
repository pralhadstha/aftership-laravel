<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Tests\Feature;

use OmniCargo\Aftership\Laravel\AfterShipManager;
use OmniCargo\Aftership\Laravel\Client\AfterShipClient;
use OmniCargo\Aftership\Laravel\Contracts\ClientInterface;
use OmniCargo\Aftership\Laravel\Contracts\DriverInterface;
use OmniCargo\Aftership\Laravel\Drivers\FakeDriver;
use OmniCargo\Aftership\Laravel\Tests\TestCase;
use OmniCargo\Aftership\Laravel\Webhooks\WebhookHandler;

final class ServiceProviderTest extends TestCase
{
    public function test_it_registers_manager(): void
    {
        $manager = $this->app->make(AfterShipManager::class);

        $this->assertInstanceOf(AfterShipManager::class, $manager);
    }

    public function test_it_registers_client_interface(): void
    {
        $client = $this->app->make(ClientInterface::class);

        $this->assertInstanceOf(AfterShipClient::class, $client);
    }

    public function test_it_registers_client_alias(): void
    {
        $client = $this->app->make(AfterShipClient::class);

        $this->assertInstanceOf(AfterShipClient::class, $client);
    }

    public function test_it_registers_driver_interface(): void
    {
        $driver = $this->app->make(DriverInterface::class);

        $this->assertInstanceOf(FakeDriver::class, $driver);
    }

    public function test_it_registers_webhook_handler(): void
    {
        $handler = $this->app->make(WebhookHandler::class);

        $this->assertInstanceOf(WebhookHandler::class, $handler);
    }

    public function test_it_resolves_singleton_instances(): void
    {
        $client1 = $this->app->make(ClientInterface::class);
        $client2 = $this->app->make(ClientInterface::class);

        $this->assertSame($client1, $client2);
    }

    public function test_config_is_loaded(): void
    {
        $this->assertSame('test-api-key', config('aftership.api_key'));
        $this->assertSame('fake', config('aftership.driver'));
        $this->assertSame(30, config('aftership.timeout'));
    }
}
