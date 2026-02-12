<?php

declare(strict_types=1);

namespace App\Entity;

use Brick\Money\Money;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;
use Siganushka\OrderBundle\Model\OrderItemSubjectInterface;

#[ORM\Entity]
class OrderItemSubject implements ResourceInterface, OrderItemSubjectInterface
{
    use ResourceTrait;

    #[ORM\Column]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?string $subtitle = null;

    #[ORM\Column(nullable: true)]
    private ?string $cover = null;

    #[ORM\Column(type: 'money')]
    private ?Money $price = null;

    #[ORM\Column(nullable: true)]
    private ?int $stock = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): static
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): static
    {
        $this->cover = $cover;

        return $this;
    }

    public function getPrice(): ?Money
    {
        return $this->price;
    }

    public function setPrice(Money $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getSubjectTitle(): string
    {
        \assert(null !== $this->title);

        return $this->title;
    }

    public function getSubjectSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function getSubjectPrice(): int
    {
        \assert(null !== $this->price);

        return $this->price->getMinorAmount()->toInt();
    }

    public function getSubjectExtra(): ?string
    {
        return $this->subtitle;
    }

    public function getSubjectImg(): ?string
    {
        return $this->cover;
    }
}
