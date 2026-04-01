<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Exceptions;

class AuthenticationException extends AfterShipException
{
    public static function invalidApiKey(): self
    {
        return new self(
            message: 'Invalid AfterShip API key. Please check your configuration.',
            code: 401,
            statusCode: 401,
            errorType: 'Unauthorized',
        );
    }

    public static function missingApiKey(): self
    {
        return new self(
            message: 'AfterShip API key is not configured. Set AFTERSHIP_API_KEY in your environment.',
            code: 401,
            statusCode: 401,
            errorType: 'Unauthorized',
        );
    }
}
