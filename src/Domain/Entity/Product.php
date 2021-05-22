<?php

declare(strict_types=1);

namespace BackendTestApp\Domain\Entity;

use BackendTestApp\Infrastructure\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ORM\Table(name="product")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("sku")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="bigint")
     * @Groups({"read"})
     */
    private int $id;

    /**
     * @Assert\Length(min=3)
     * @Assert\NotBlank(groups={"create"})
     * @ORM\Column(type="string", length=64,  unique=true)
     * @Groups({"read", "create", "update"})
     */
    private string $sku;

    /**
     * @Assert\NotBlank(groups={"create"})
     * @ORM\Column(type="string", length=100, nullable=false)
     * @Groups({"read", "create", "update"})
     */
    private string $title;

    /**
     *
     * @ORM\Column(type="integer")
     * @Groups({"read", "create", "update"})
     */
    private int $qty = 1;

    /**
     *@Assert\Type("integer")
     * @ORM\Column(type="bigint")
     * @Groups({"read", "create", "update"})
     */
    private int $price = 0;

    /**
     * @Assert\Type("integer")
     * @ORM\Column(type="bigint")
     * @Groups({"read"})
     */
    private int $sum_qty = 0;


    /**
     * @Assert\Type(type="\DateTimeInterface", message="Custom Message")
     * @ORM\Column(type="datetime")
     * @Groups({"read"})
     */
    private \DateTimeInterface $created_at;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     * @Groups({"read"})
     */
    private ?int $userId = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     */
    public function setSku(string $sku): void
    {
        $this->sku = $sku;
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
    public function setQty(int $qty): void
    {
        $this->qty = $qty;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getSumQty(): int
    {
        return $this->sum_qty;
    }

    /**
     * @param int $sum_qty
     */
    public function setSumQty(int $sum_qty): void
    {
        $this->sum_qty = $sum_qty;
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
     * @ORM\PrePersist
     */
    public function setSumQtyValue()
    {
        $this->sum_qty = $this->qty * $this->price;
    }
}
