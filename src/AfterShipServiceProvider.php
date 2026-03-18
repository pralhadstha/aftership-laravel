<?php

declare(strict_types=1);

namespace AfterShip;

use AfterShip\Client\AfterShipClient;
use AfterShip\Contracts\ClientInterface;
use AfterShip\Contracts\DriverInterface;
use AfterShip\Webhooks\WebhookHandler;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\ServiceProvider;

final class AfterShipServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/aftership.php', 'aftership');

        $this->app->singleton(AfterShipManager::class, function ($app) {
            /** @var array<string, mixed> $config */
            $config = $app['config']->get('aftership', []);

            return new AfterShipManager(
                config: $config,
                httpFactory: $app->make(HttpFactory::class),
            );
        });

        $this->app->singleton(ClientInterface::class, function ($app) {
            return $app->make(AfterShipManager::class)->client();
        });

        $this->app->alias(ClientInterface::class, AfterShipClient::class);

        $this->app->singleton(DriverInterface::class, function ($app) {
            return $app->make(ClientInterface::class)->driver();
        });

        $this->app->singleton(WebhookHandler::class, function ($app) {
            /** @var string $secret */
            $secret = $app['config']->get('aftership.webhook_secret', '');

            return new WebhookHandler($secret);
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/aftership.php' => config_path('aftership.php'),
            ], 'aftership-config');
        }
    }

    /**
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            AfterShipManager::class,
            ClientInterface::class,
            AfterShipClient::class,
            DriverInterface::class,
            WebhookHandler::class,
        ];
    }
}
