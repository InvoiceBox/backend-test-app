<?php

declare(strict_types=1);

namespace BackendTestApp\Domain\Exception;

class InvalidArgument extends BackendTestAppException
{
    protected $statusCode = 422;
}
