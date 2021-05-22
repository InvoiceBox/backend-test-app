<?php

declare(strict_types=1);

namespace BackendTestApp\Domain\Entity;

use BackendTestApp\Infrastructure\Repository\OrderItemRepository;
use JMS\Serializer\Annotation\Groups;
use BackendTestApp\Domain\Entity\Product;
use BackendTestApp\Domain\Entity\Order;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderItemRepository::class)
 */
class OrderItem
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="bigint")
     * @Groups({"read"})
     */
    private int $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read", "create", "update"})
     * @var int
     */
    private int $qty = 1;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"read", "create", "update"})
     * @var Order
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"read", "create", "update"})
     * @var Product
     */
    private $product;

    public function getId(): int
    {
        return $this->id;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;
        return $this;
    }


    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return int
     */
    public function getQty(): int
    {
        return $this->qty;
    }

    /**
     * @param int $qty
     */
    public function setQty(int $qty): self
    {
        $this->qty = $qty;
        return $this;
    }

    /**
     * @param \BackendTestApp\Domain\Entity\OrderItem $item
     * @return bool
     */
    public function containsItem(OrderItem $item): bool
    {
        return $this->getProduct()->getId() === $item->getProduct()->getId();
    }

    public function hasProductId(int $id): bool
    {
        return $this->getProduct()->getId() === $id;
    }

    /**
     * OrderItem total price
     * @return int
     */
    public function getTotal(): int
    {
        return $this->getProduct()->getPrice() * $this->getQty();
    }

}