<?php

declare(strict_types=1);

namespace BackendTestApp\Domain\Exception;

class NotFound extends BackendTestAppException
{
    protected $statusCode = 404;

}
