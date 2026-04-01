<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Exceptions;

class ApiException extends AfterShipException
{
    /**
     * @param array<string, mixed> $responseBody
     */
    public static function fromResponse(int $statusCode, array $responseBody): self
    {
        $meta = $responseBody['meta'] ?? [];
        $message = $meta['message'] ?? 'An API error occurred';
        $code = $meta['code'] ?? $statusCode;
        $type = $meta['type'] ?? null;

        return new self(
            message: $message,
            code: (int) $code,
            statusCode: $statusCode,
            errorType: $type,
        );
    }
}
