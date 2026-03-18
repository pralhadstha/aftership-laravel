<?php

declare(strict_types=1);

namespace AfterShip\Tests\Unit\Support;

use AfterShip\Exceptions\ApiException;
use AfterShip\Exceptions\AuthenticationException;
use AfterShip\Exceptions\RateLimitException;
use AfterShip\Support\ExceptionMapper;
use PHPUnit\Framework\TestCase;

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
}
