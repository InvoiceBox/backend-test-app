<?php

declare(strict_types=1);

namespace BackendTestApp\Domain\Entity;

use BackendTestApp\Infrastructure\Repository\OrderItemRepository;
use BackendTestApp\Infrastructure\Repository\OrderRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\QuoteStrategy;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="order_")
 * @ORM\HasLifecycleCallbacks()
 */
class Order
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="bigint")
     * @Groups({"read"})
     */
    private int $id;


    /**
     * @ORM\OneToMany(targetEntity=OrderItem::class, mappedBy="order", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Groups({"read"})
     * @var ArrayCollection
     */
    private $items;


    /**
     * @Assert\Type("integer")
     * @ORM\Column(type="bigint")
     * @Groups({"read"})
     */
    private int $total_price = 0;


    /**
     * @Assert\Type(type="\DateTimeInterface")
     * @ORM\Column(type="datetime")
     * @Groups({"read"})
     */
    private \DateTimeInterface $created_at;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     * @Groups({"read"})
     */
    private ?int $userId = null;


    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Collection|OrderItem[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }


    /**
     * @param  int  $id
     * @return \BackendTestApp\Domain\Entity\OrderItem|null
     */
    public function getItemByProductId(int $id): ?OrderItem
    {
        foreach ($this->getItems() as $item){
            if ($item->hasProductId($id)){
                return $item;
            }
        }
        return null;
    }

    /**
     * @param OrderItem $item
     * @return $this
     */
    public function addItem(OrderItem $newItem): self
    {
        foreach ($this->getItems() as $item) {
            if ($item->containsItem($newItem)) {
                $item->setQty($item->getQty() + $newItem->getQty());
                return $this;
            }
        }
        $this->items[] = $newItem;
        $newItem->setOrder($this);
        return $this;
    }


    public function deleteItem(OrderItem $item): self
    {
        if ($this->items->removeElement($item)) {
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
        }
        return $this;
    }

    /**
     * Delete all items
     * @return $this
     */
    public function deleteAllItems(): self
    {
        foreach ($this->getItems() as $item) {
            $this->deleteItem($item);
        }

        return $this;
    }


    /**
     * @return int
     */
    public function getTotalPrice(): int
    {
        return $this->total_price;
    }

    /**
     * @param int $price
     */
    public function setTotalPrice(int $price): void
    {
        $this->total_price = $price;
    }


    /**
     * @return mixed
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt(\DateTimeInterface $created_at): void
    {
        $this->created_at = $created_at;
    }


    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {

        $this->created_at = new \DateTime("now");
    }

    /**
     * @ORM\PreUpdate
     */
    public function setSumQtyValue()
    {
        $this->total_price = $this->getTotal();
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        $total_price = 0;
        foreach ($this->items as $item) {
            $total_price += $item->getTotal();
        }

        return $total_price;
    }

}
