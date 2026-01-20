<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductVariantRepository;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\OrderBundle\Model\OrderItemSubjectInterface;
use Siganushka\OrderBundle\Model\StockableInterface;
use Siganushka\ProductBundle\Entity\ProductVariant as BaseProductVariant;

#[ORM\Entity(repositoryClass: ProductVariantRepository::class)]
class ProductVariant extends BaseProductVariant implements OrderItemSubjectInterface, StockableInterface
{
    public function getSubjectTitle(): string
    {
        $title = $this->product?->getName();
        \assert(null !== $title);

        return $title;
    }

    public function getSubjectPrice(): int
    {
        \assert(null !== $this->price);

        return $this->price;
    }

    public function getSubjectExtra(): ?string
    {
        return $this->name;
    }

    public function getSubjectImg(): ?string
    {
        return ($this->img ?? $this->product?->getImg())?->getUrl();
    }

    public function availableStock(): ?int
    {
        return $this->stock;
    }

    public function decrementStock(int $quantity): void
    {
        \assert(null !== $this->stock);

        $this->stock -= $quantity;
    }

    public function incrementStock(int $quantity): void
    {
        \assert(null !== $this->stock);

        $this->stock += $quantity;
    }
}
