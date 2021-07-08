<?php


namespace BackendTestApp\Application\DTO;

use JMS\Serializer\Annotation\Groups;

class ProductByIdAndCount
{
    /**
     * @Groups({"read", "create", "update"})
     */
    private int $productId;
    /**
     * @Groups({"read", "create", "update"})
     */
    private int $count;

    public function __construct(int $productId = -1, int $count = 0)
    {
        $this->productId = $productId;
        $this->count = $count;
    }

    public function getCountAndId()
    {
        return [$this->productId, $this->count];
    }
}