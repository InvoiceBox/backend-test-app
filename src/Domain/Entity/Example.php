<?php

declare(strict_types=1);

namespace BackendTestApp\Domain\Entity;

use BackendTestApp\Infrastructure\Repository\ExampleRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ExampleRepository::class)
 * @ORM\Table(name="example")
 */
class Example
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="bigint")
     * @Groups({"read"})
     */
    private int $id;

    /**
     * @Assert\NotBlank(groups={"create"})
     * @ORM\Column(type="string", length=100, nullable=false)
     * @Groups({"read", "create", "update"})
     */
    private string $title;

    /**
     * @Assert\NotBlank(groups={"create"})
     * @ORM\Column(type="string", length=500, nullable=false)
     * @Groups({"read", "create", "update"})
     */
    private string $description;

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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }
}
