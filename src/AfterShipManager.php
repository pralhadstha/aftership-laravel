<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel;

use OmniCargo\Aftership\Laravel\Client\AfterShipClient;
use OmniCargo\Aftership\Laravel\Contracts\ClientInterface;
use OmniCargo\Aftership\Laravel\Contracts\DriverInterface;
use OmniCargo\Aftership\Laravel\Drivers\FakeDriver;
use OmniCargo\Aftership\Laravel\Drivers\HttpDriver;
use OmniCargo\Aftership\Laravel\Drivers\SdkDriver;
use OmniCargo\Aftership\Laravel\Exceptions\InvalidConfigurationException;
use Illuminate\Http\Client\Factory as HttpFactory;

final class AfterShipManager
{
    /** @var array<string, ClientInterface> */
    private array $clients = [];

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config,
        private readonly ?HttpFactory $httpFactory = null,
    ) {}

    /**
     * Get a client instance for the given driver.
     */
    public function client(?string $driver = null): ClientInterface
    {
        $driver ??= $this->getDefaultDriver();

        if (!isset($this->clients[$driver])) {
            $this->clients[$driver] = $this->createClient($driver);
        }

        return $this->clients[$driver];
    }

    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->config['driver'] ?? 'sdk';
    }

    /**
     * Create a client instance for the given driver.
     */
    private function createClient(string $driver): ClientInterface
    {
        $driverInstance = $this->createDriver($driver);

        return new AfterShipClient($driverInstance);
    }

    /**
     * Create a driver instance.
     */
    private function createDriver(string $driver): DriverInterface
    {
        return match ($driver) {
            'sdk' => $this->createSdkDriver(),
            'http' => $this->createHttpDriver(),
            'fake' => $this->createFakeDriver(),
            default => throw InvalidConfigurationException::invalidDriver($driver),
        };
    }

    private function createSdkDriver(): SdkDriver
    {
        return new SdkDriver(
            apiKey: $this->getApiKey(),
            baseUrl: $this->getBaseUrl(),
            timeout: $this->getTimeout(),
        );
    }

    private function createHttpDriver(): HttpDriver
    {
        if ($this->httpFactory === null) {
            throw new InvalidConfigurationException(
                'The HTTP driver requires the Laravel HTTP client factory.'
            );
        }

        return new HttpDriver(
            httpFactory: $this->httpFactory,
            apiKey: $this->getApiKey(),
            baseUrl: $this->getBaseUrl(),
            timeout: $this->getTimeout(),
            apiVersion: $this->getApiVersion(),
        );
    }

    private function createFakeDriver(): FakeDriver
    {
        return new FakeDriver();
    }

    private function getApiKey(): string
    {
        $apiKey = $this->config['api_key'] ?? '';

        if ($apiKey === '') {
            throw InvalidConfigurationException::missingConfiguration('api_key');
        }

        return $apiKey;
    }

    private function getBaseUrl(): string
    {
        return $this->config['base_url'] ?? 'https://api.aftership.com';
    }

    private function getTimeout(): int
    {
        return (int) ($this->config['timeout'] ?? 30);
    }

    private function getApiVersion(): string
    {
        return $this->config['api_version'] ?? 'tracking/2026-01';
    }
}
