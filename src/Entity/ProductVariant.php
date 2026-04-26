<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductVariantRepository;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\OrderBundle\Model\OrderItemSubjectData;
use Siganushka\OrderBundle\Model\OrderItemSubjectInterface;
use Siganushka\OrderBundle\Model\StockableInterface;
use Siganushka\ProductBundle\Entity\ProductVariant as BaseProductVariant;

#[ORM\Entity(repositoryClass: ProductVariantRepository::class)]
class ProductVariant extends BaseProductVariant implements OrderItemSubjectInterface, StockableInterface
{
    public function createForOrderItem(int $quantity): OrderItemSubjectData
    {
        return new OrderItemSubjectData(
            title: $this->product?->getName(),
            price: $this->price,
            subtitle: $this->name,
            img: $this->product?->getImg()?->getUrl(),
        );
    }

    public function availableStock(): ?int
    {
        return $this->stock;
    }

    public function decrementStock(int $quantity): void
    {
        \is_int($this->stock) && $this->stock -= $quantity;
    }

    public function incrementStock(int $quantity): void
    {
        \is_int($this->stock) && $this->stock += $quantity;
    }
}
