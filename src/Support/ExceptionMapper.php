<?php

declare(strict_types=1);

namespace AfterShip\Support;

use AfterShip\Exceptions\AfterShipException;
use AfterShip\Exceptions\ApiException;
use AfterShip\Exceptions\AuthenticationException;
use AfterShip\Exceptions\RateLimitException;
use Tracking\Exception\AfterShipError;

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

    /**
     * Map an AfterShip SDK exception to a package exception.
     */
    public static function fromSdkException(AfterShipError $e): AfterShipException
    {
        $statusCode = (int) $e->getStatusCode();

        return match (true) {
            $statusCode === 401 => AuthenticationException::invalidApiKey(),
            $statusCode === 429 => RateLimitException::exceeded(),
            default => new ApiException(
                message: $e->getMessage(),
                code: (int) $e->getCode(),
                previous: $e,
                statusCode: $statusCode,
                errorType: null,
            ),
        };
    }
}
