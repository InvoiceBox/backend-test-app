<?php

declare(strict_types=1);

namespace BackendTestApp\Domain\Exception;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Exception;

class BackendTestAppException extends Exception implements
    HttpExceptionInterface
{
    protected $message = 'Error';
    protected $statusCode = 400;
    protected ?FieldErrorCollection $fieldErrorCollection = null;

    public function __construct(
        string $message = null,
        ?FieldErrorCollection $fieldErrorCollection = null
    ) {
        parent::__construct($message ?? $this->message);

        $this->fieldErrorCollection = $fieldErrorCollection;
    }

    public function getFieldErrorCollection(): ?FieldErrorCollection
    {
        return $this->fieldErrorCollection;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return [];
    }
}
