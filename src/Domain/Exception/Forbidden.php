<?php

declare(strict_types=1);

namespace BackendTestApp\Domain\Exception;

class Forbidden extends BackendTestAppException
{
    protected $message = 'Forbidden';
    protected $statusCode = 403;
}

