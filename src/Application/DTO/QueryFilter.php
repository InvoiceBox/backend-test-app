<?php

declare(strict_types=1);

namespace BackendTestApp\Application\DTO;

class QueryFilter
{
    public const DEFAULT_PAGE_SIZE = 30;
    public const PAGE_SIZE = '_pageSize';
    public const PAGE = '_page';
    public const ORDER = '_order';

    private int $pageSize;
    private int $page;
    private array $order = [];


    public function __construct(array $params)
    {
        $this->pageSize = (int)($params[static::PAGE_SIZE] ?? self::DEFAULT_PAGE_SIZE);
        $this->page = (int)($params[static::PAGE] ?? 1);
        $this->order = (array)($params[static::ORDER] ?? []);
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getOrder(): array
    {
        return $this->order;
    }
}

