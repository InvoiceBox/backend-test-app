<?php


namespace BackendTestApp\Application\DTO;

use JMS\Serializer\Annotation\Groups;

final class AddToOrderRequest
{
    /**
     * @Groups({"read"})
     * @var int|null
     */
    public ?int $order_id = null;


    /**
     * @Groups({"read"})
     * @var int|null
     */
    public ?int $product_id = null;
}