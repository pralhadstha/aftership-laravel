<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Tests\Unit\Exceptions;

use OmniCargo\Aftership\Laravel\Exceptions\AfterShipException;
use OmniCargo\Aftership\Laravel\Exceptions\ApiException;
use OmniCargo\Aftership\Laravel\Exceptions\AuthenticationException;
use OmniCargo\Aftership\Laravel\Exceptions\InvalidConfigurationException;
use OmniCargo\Aftership\Laravel\Exceptions\RateLimitException;
use PHPUnit\Framework\TestCase;

final class ExceptionTest extends TestCase
{
    public function test_aftership_exception_properties(): void
    {
        $exception = new AfterShipException(
            message: 'Test error',
            code: 400,
            statusCode: 400,
            errorType: 'BadRequest',
        );

        $this->assertSame('Test error', $exception->getMessage());
        $this->assertSame(400, $exception->getCode());
        $this->assertSame(400, $exception->getStatusCode());
        $this->assertSame('BadRequest', $exception->getErrorType());
    }

    public function test_api_exception_from_response(): void
    {
        $exception = ApiException::fromResponse(422, [
            'meta' => [
                'code' => 4012,
                'message' => 'Tracking already exists',
                'type' => 'BadRequest',
            ],
        ]);

        $this->assertSame('Tracking already exists', $exception->getMessage());
        $this->assertSame(4012, $exception->getCode());
        $this->assertSame(422, $exception->getStatusCode());
        $this->assertSame('BadRequest', $exception->getErrorType());
    }

    public function test_api_exception_handles_empty_meta(): void
    {
        $exception = ApiException::fromResponse(500, []);

        $this->assertSame('An API error occurred', $exception->getMessage());
        $this->assertSame(500, $exception->getStatusCode());
    }

    public function test_authentication_exception_invalid_key(): void
    {
        $exception = AuthenticationException::invalidApiKey();

        $this->assertSame(401, $exception->getCode());
        $this->assertSame(401, $exception->getStatusCode());
        $this->assertStringContainsString('Invalid', $exception->getMessage());
    }

    public function test_authentication_exception_missing_key(): void
    {
        $exception = AuthenticationException::missingApiKey();

        $this->assertSame(401, $exception->getCode());
        $this->assertStringContainsString('not configured', $exception->getMessage());
    }

    public function test_rate_limit_exception(): void
    {
        $exception = RateLimitException::exceeded(60);

        $this->assertSame(429, $exception->getCode());
        $this->assertSame(429, $exception->getStatusCode());
        $this->assertStringContainsString('60 seconds', $exception->getMessage());
    }

    public function test_rate_limit_exception_without_retry(): void
    {
        $exception = RateLimitException::exceeded();

        $this->assertStringNotContainsString('seconds', $exception->getMessage());
    }

    public function test_invalid_configuration_invalid_driver(): void
    {
        $exception = InvalidConfigurationException::invalidDriver('banana');

        $this->assertStringContainsString('banana', $exception->getMessage());
    }

    public function test_invalid_configuration_missing(): void
    {
        $exception = InvalidConfigurationException::missingConfiguration('api_key');

        $this->assertStringContainsString('api_key', $exception->getMessage());
    }

    public function test_exception_hierarchy(): void
    {
        $this->assertInstanceOf(\RuntimeException::class, new AfterShipException());
        $this->assertInstanceOf(AfterShipException::class, new ApiException());
        $this->assertInstanceOf(AfterShipException::class, new AuthenticationException());
        $this->assertInstanceOf(AfterShipException::class, new RateLimitException());
        $this->assertInstanceOf(AfterShipException::class, new InvalidConfigurationException());
    }
}
