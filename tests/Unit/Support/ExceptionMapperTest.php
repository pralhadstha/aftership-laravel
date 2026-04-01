<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Tests\Unit\Support;

use OmniCargo\Aftership\Laravel\Exceptions\ApiException;
use OmniCargo\Aftership\Laravel\Exceptions\AuthenticationException;
use OmniCargo\Aftership\Laravel\Exceptions\RateLimitException;
use OmniCargo\Aftership\Laravel\Support\ExceptionMapper;
use PHPUnit\Framework\TestCase;
use Tracking\Exception\AfterShipError;

final class ExceptionMapperTest extends TestCase
{
    public function test_it_maps_401_to_authentication_exception(): void
    {
        $exception = ExceptionMapper::fromHttpResponse(401, [
            'meta' => ['code' => 401, 'message' => 'Unauthorized', 'type' => 'Unauthorized'],
        ]);

        $this->assertInstanceOf(AuthenticationException::class, $exception);
        $this->assertSame(401, $exception->getStatusCode());
    }

    public function test_it_maps_429_to_rate_limit_exception(): void
    {
        $exception = ExceptionMapper::fromHttpResponse(429, [
            'meta' => ['code' => 429, 'message' => 'Too Many Requests'],
        ]);

        $this->assertInstanceOf(RateLimitException::class, $exception);
        $this->assertSame(429, $exception->getStatusCode());
    }

    public function test_it_maps_other_errors_to_api_exception(): void
    {
        $exception = ExceptionMapper::fromHttpResponse(500, [
            'meta' => ['code' => 500, 'message' => 'Internal Server Error', 'type' => 'InternalError'],
        ]);

        $this->assertInstanceOf(ApiException::class, $exception);
        $this->assertSame(500, $exception->getStatusCode());
        $this->assertSame('Internal Server Error', $exception->getMessage());
        $this->assertSame('InternalError', $exception->getErrorType());
    }

    public function test_it_handles_empty_response_body(): void
    {
        $exception = ExceptionMapper::fromHttpResponse(400, []);

        $this->assertInstanceOf(ApiException::class, $exception);
        $this->assertSame(400, $exception->getStatusCode());
    }

    public function test_it_maps_sdk_401_to_authentication_exception(): void
    {
        $sdkError = new AfterShipError('Unauthorized', 401, 401);
        $exception = ExceptionMapper::fromSdkException($sdkError);

        $this->assertInstanceOf(AuthenticationException::class, $exception);
        $this->assertSame(401, $exception->getStatusCode());
    }

    public function test_it_maps_sdk_429_to_rate_limit_exception(): void
    {
        $sdkError = new AfterShipError('Too Many Requests', 429, 429);
        $exception = ExceptionMapper::fromSdkException($sdkError);

        $this->assertInstanceOf(RateLimitException::class, $exception);
        $this->assertSame(429, $exception->getStatusCode());
    }

    public function test_it_maps_sdk_other_errors_to_api_exception(): void
    {
        $sdkError = new AfterShipError('Bad Request', 4001, 400);
        $exception = ExceptionMapper::fromSdkException($sdkError);

        $this->assertInstanceOf(ApiException::class, $exception);
        $this->assertSame(400, $exception->getStatusCode());
        $this->assertSame('Bad Request', $exception->getMessage());
    }
}
