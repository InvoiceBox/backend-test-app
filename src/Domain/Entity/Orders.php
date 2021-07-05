<?php

declare(strict_types=1);

namespace BackendTestApp\Domain\Entity;

use BackendTestApp\Infrastructure\Repository\OrderRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="orders")
 */
class Orders
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private int $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read", "create", "update"})
     */
    private int $userId;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read", "create", "update"})
     */
    private int $amount;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read", "create", "update"})
     */
    private $creationDate;

    /**
     * @ORM\OneToMany(targetEntity=OrderProduct::class, mappedBy="orderId")
     */
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function setCreationDate($creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

}
