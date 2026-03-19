<?php

declare(strict_types=1);

namespace AfterShip\Tests\Feature;

use AfterShip\Console\InstallCommand;
use AfterShip\Tests\TestCase;

final class InstallCommandTest extends TestCase
{
    public function test_command_is_registered(): void
    {
        $this->assertArrayHasKey('aftership:install', $this->app->make('Illuminate\Contracts\Console\Kernel')->all());
    }

    public function test_set_env_value_adds_new_key(): void
    {
        $command = new InstallCommand();

        $env = "APP_NAME=Laravel\nAPP_ENV=local\n";
        $result = $command->setEnvValue($env, 'AFTERSHIP_DRIVER', 'sdk');

        $this->assertStringContainsString('AFTERSHIP_DRIVER=sdk', $result);
        $this->assertStringContainsString('APP_NAME=Laravel', $result);
    }

    public function test_set_env_value_updates_existing_key(): void
    {
        $command = new InstallCommand();

        $env = "APP_NAME=Laravel\nAFTERSHIP_DRIVER=http\nAPP_ENV=local\n";
        $result = $command->setEnvValue($env, 'AFTERSHIP_DRIVER', 'sdk');

        $this->assertStringContainsString('AFTERSHIP_DRIVER=sdk', $result);
        $this->assertStringNotContainsString('AFTERSHIP_DRIVER=http', $result);
        $this->assertSame(1, substr_count($result, 'AFTERSHIP_DRIVER'));
    }

    public function test_set_env_value_does_not_affect_other_keys(): void
    {
        $command = new InstallCommand();

        $env = "AFTERSHIP_API_KEY=old-key\nAFTERSHIP_DRIVER=http\n";
        $result = $command->setEnvValue($env, 'AFTERSHIP_DRIVER', 'sdk');

        $this->assertStringContainsString('AFTERSHIP_API_KEY=old-key', $result);
        $this->assertStringContainsString('AFTERSHIP_DRIVER=sdk', $result);
    }

    public function test_set_env_value_handles_empty_content(): void
    {
        $command = new InstallCommand();

        $result = $command->setEnvValue('', 'AFTERSHIP_DRIVER', 'sdk');

        $this->assertStringContainsString('AFTERSHIP_DRIVER=sdk', $result);
    }
}
