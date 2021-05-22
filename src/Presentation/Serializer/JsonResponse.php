<?php

declare(strict_types=1);

namespace BackendTestApp\Presentation\Serializer;

use Symfony\Component\HttpFoundation\JsonResponse as BaseResponse;

class JsonResponse extends BaseResponse
{
    public function __construct(array $responseData = null, ?int $status = 200)
    {
        parent::__construct('', $status);

        $this->setData(['data' => $responseData]);
    }
}
