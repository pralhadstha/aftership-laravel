<?php

declare(strict_types=1);

namespace AfterShip\Tests;

use AfterShip\AfterShipServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            AfterShipServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array<string, class-string>
     */
    protected function getPackageAliases($app): array
    {
        return [
            'AfterShip' => \AfterShip\Facades\AfterShip::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('aftership.api_key', 'test-api-key');
        $app['config']->set('aftership.driver', 'fake');
        $app['config']->set('aftership.base_url', 'https://api.aftership.com/v4');
        $app['config']->set('aftership.timeout', 30);
        $app['config']->set('aftership.webhook_secret', 'test-webhook-secret');
    }
}
