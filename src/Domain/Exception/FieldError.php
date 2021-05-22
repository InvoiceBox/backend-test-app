<?php

declare(strict_types=1);

namespace BackendTestApp\Domain\Exception;

class FieldError
{
    public const TYPE_WRONG_VALUE = 'wrong_value';

    private string $code;

    public function __construct(private string $field, private string $message, ?string $code = null)
    {
        $this->code = $code ?? self::TYPE_WRONG_VALUE;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function toArray() : array
    {
        return [
            'name' => $this->field,
            'code' => $this->code,
            'message' => $this->message,
        ];
    }
}
