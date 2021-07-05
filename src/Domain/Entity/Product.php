<?php

declare(strict_types=1);

namespace BackendTestApp\Domain\Entity;

use BackendTestApp\Infrastructure\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ORM\Table(name="product")
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "create", "update"})
     */
    private string $title;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read", "create", "update"})
     */
    private int $price;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read", "create", "update"})
     */
    private int $count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read", "create", "update"})
     */
    private ?int $priceForAll;

    /**
     * @ORM\OneToMany(targetEntity=OrderProduct::class, mappedBy="productId")
     */
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        if ($count < 0){
            throw new \Exception("invalid number of products");
        }
        $this->count = $count;

        return $this;
    }

    public function getPriceForAll(): ?int
    {
        return $this->priceForAll;
    }

    public function setPriceForAll(int $priceForAll): self
    {
        $this->priceForAll = $priceForAll;

        return $this;
    }

    public function updateRemoveCountAndPriceForAll(){
        $this->setCount($this->count - 1);
        $this->setPriceForAll($this->count * $this->price);
    }

    public function updateAddCountAndPriceForAll(){
        $this->setCount($this->count + 1);
        $this->setPriceForAll($this->count * $this->price);
    }
}
