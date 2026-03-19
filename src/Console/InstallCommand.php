<?php

declare(strict_types=1);

namespace AfterShip\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\info;
use function Laravel\Prompts\select;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\text;

final class InstallCommand extends Command
{
    /** @var string */
    protected $signature = 'aftership:install';

    /** @var string */
    protected $description = 'Install and configure the AfterShip package';

    public function handle(): int
    {
        info('AfterShip Laravel SDK Installer');

        $driver = $this->promptDriver();

        if ($driver === 'sdk') {
            $this->installSdkPackage();
        }

        $apiKey = $this->promptApiKey();

        $this->writeEnvironmentValues($driver, $apiKey);

        $this->publishConfig();

        $this->displaySummary($driver, $apiKey);

        return self::SUCCESS;
    }

    private function promptDriver(): string
    {
        /** @var string $driver */
        $driver = select(
            label: 'Which driver would you like to use?',
            options: [
                'sdk' => 'SDK — Official AfterShip PHP SDK (requires aftership/tracking-sdk)',
                'http' => 'HTTP — Laravel HTTP client (no extra dependencies)',
            ],
            default: 'sdk',
        );

        return $driver;
    }

    private function installSdkPackage(): void
    {
        if (class_exists(\Tracking\Client::class)) {
            info('AfterShip Tracking SDK is already installed.');

            return;
        }

        info('Installing aftership/tracking-sdk...');

        $result = false;

        progress(
            label: 'Running composer require aftership/tracking-sdk...',
            steps: ['install'],
            callback: function () use (&$result): void {
                $process = new Process(['composer', 'require', 'aftership/tracking-sdk']);
                $process->setTimeout(120);
                $process->run();
                $result = $process->isSuccessful();
            },
        );

        if ($result) {
            info('AfterShip Tracking SDK installed successfully.');
        } else {
            $this->components->warn('Failed to install aftership/tracking-sdk. You can install it manually:');
            $this->components->bulletList(['composer require aftership/tracking-sdk']);
        }
    }

    private function promptApiKey(): string
    {
        return text(
            label: 'Enter your AfterShip API key',
            placeholder: 'your-api-key',
            hint: 'Leave empty to configure later in your .env file',
        );
    }

    private function writeEnvironmentValues(string $driver, string $apiKey): void
    {
        $envPath = $this->laravel->basePath('.env');

        if (!file_exists($envPath)) {
            $this->components->warn('.env file not found. Skipping environment configuration.');

            return;
        }

        $env = file_get_contents($envPath);

        if ($env === false) {
            return;
        }

        $env = $this->setEnvValue($env, 'AFTERSHIP_DRIVER', $driver);

        if ($apiKey !== '') {
            $env = $this->setEnvValue($env, 'AFTERSHIP_API_KEY', $apiKey);
        }

        file_put_contents($envPath, $env);

        info('Environment file updated.');
    }

    /**
     * Set or update an environment variable value in the .env content string.
     */
    public function setEnvValue(string $envContent, string $key, string $value): string
    {
        $pattern = '/^' . preg_quote($key, '/') . '=.*/m';

        if (preg_match($pattern, $envContent)) {
            return (string) preg_replace($pattern, "{$key}={$value}", $envContent);
        }

        return rtrim($envContent, "\n") . "\n{$key}={$value}\n";
    }

    private function publishConfig(): void
    {
        $this->call('vendor:publish', [
            '--tag' => 'aftership-config',
        ]);
    }

    private function displaySummary(string $driver, string $apiKey): void
    {
        $this->newLine();
        info('AfterShip has been configured successfully!');
        $this->newLine();

        $this->components->bulletList([
            "Driver: {$driver}",
            'API Key: ' . ($apiKey !== '' ? 'configured' : 'not set — add AFTERSHIP_API_KEY to .env'),
            'Config: config/aftership.php published',
        ]);
    }
}
