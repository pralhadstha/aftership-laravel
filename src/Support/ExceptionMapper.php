<?php

declare(strict_types=1);

namespace AfterShip\Support;

use AfterShip\Exceptions\AfterShipException;
use AfterShip\Exceptions\ApiException;
use AfterShip\Exceptions\AuthenticationException;
use AfterShip\Exceptions\RateLimitException;

final class ExceptionMapper
{
    /**
     * Map an HTTP status code and response body to the appropriate exception.
     *
     * @param array<string, mixed> $responseBody
     */
    public static function fromHttpResponse(int $statusCode, array $responseBody): AfterShipException
    {
        return match (true) {
            $statusCode === 401 => AuthenticationException::invalidApiKey(),
            $statusCode === 429 => RateLimitException::exceeded(),
            default => ApiException::fromResponse($statusCode, $responseBody),
        };
    }
}
