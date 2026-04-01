<?php

declare(strict_types=1);

namespace OmniCargo\Aftership\Laravel\Exceptions;

use RuntimeException;

class AfterShipException extends RuntimeException
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        protected readonly ?int $statusCode = null,
        protected readonly ?string $errorType = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getErrorType(): ?string
    {
        return $this->errorType;
    }
}
