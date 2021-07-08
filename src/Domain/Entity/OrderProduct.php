<?php


namespace BackendTestApp\Domain\Entity;

use BackendTestApp\Infrastructure\Repository\OrderProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=OrderProductRepository::class)
 * @ORM\Table(name="order_product")
 */
class OrderProduct
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="products")
     * @ORM\JoinColumn(name="orders", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private ?Order $orderId;

    /**
     * @ORM\ManyToOne (targetEntity=Product::class, inversedBy="orders")
     * @ORM\JoinColumn(name="product", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private ?Product $productId;


    public function removeProduct(): self
    {
        $this->productId = null;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): ?Order
    {
        return $this->orderId;
    }

    public function setOrderId(?Order $order): self
    {
        $this->orderId = $order;

        return $this;
    }

    public function getProductId(): ?Product
    {
        return $this->productId;
    }

    public function setProductId(?Product $product): self
    {
        $this->productId = $product;

        return $this;
    }
}