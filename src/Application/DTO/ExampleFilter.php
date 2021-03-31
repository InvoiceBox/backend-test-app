<?php

declare(strict_types=1);

namespace BackendTestApp\Application\DTO;

class ExampleFilter extends QueryFilter
{
    private string $query;

    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->query = $params['query'] ?? '';
    }

    public function getQuery(): string
    {
        return $this->query;
    }
}