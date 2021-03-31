<?php

declare(strict_types=1);

namespace BackendTestApp\Domain\Exception;

class Unauthorized extends BackendTestAppException
{
    protected $statusCode = 401;
}
