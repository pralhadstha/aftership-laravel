<?php

declare(strict_types=1);

namespace AfterShip\Exceptions;

class InvalidConfigurationException extends AfterShipException
{
    public static function invalidDriver(string $driver): self
    {
        return new self(
            message: "Invalid AfterShip driver [{$driver}]. Supported drivers: sdk, http.",
            code: 0,
        );
    }

    public static function missingConfiguration(string $key): self
    {
        return new self(
            message: "Missing required AfterShip configuration: [{$key}].",
            code: 0,
        );
    }
}
