<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Exceptions;

class RateLimitException extends AfterShipException
{
    public static function exceeded(int $retryAfter = 0): self
    {
        $message = 'AfterShip API rate limit exceeded.';
        if ($retryAfter > 0) {
            $message .= " Retry after {$retryAfter} seconds.";
        }

        return new self(
            message: $message,
            code: 429,
            statusCode: 429,
            errorType: 'TooManyRequests',
        );
    }
}
