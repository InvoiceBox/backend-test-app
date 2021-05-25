<?php


namespace BackendTestApp\Application\DTO;

use JMS\Serializer\Annotation\Groups;

final class AddToOrderRequest
{
    /**
     * @Groups({"read"})
     * @var int|null
     */
    public ?int $orderId = null;


    /**
     * @Groups({"read"})
     * @var int|null
     */
    public ?int $productId = null;

    /**
     * @Groups({"read"})
     * @var int|null
     */
    public ?int $itemId = null;
}