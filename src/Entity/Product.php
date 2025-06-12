<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\ProductBundle\Entity\Product as BaseProduct;

#[ORM\Entity]
class Product extends BaseProduct
{
    #[ORM\Column('`virtual`', type: Types::BOOLEAN)]
    protected bool $virtual = false;

    #[ORM\Column(nullable: true)]
    protected ?int $weight = null;

    #[ORM\Column(nullable: true)]
    protected ?int $length = null;

    #[ORM\Column(nullable: true)]
    protected ?int $width = null;

    #[ORM\Column(nullable: true)]
    protected ?int $height = null;

    public function isVirtual(): bool
    {
        return $this->virtual;
    }

    public function getVirtual(): bool
    {
        return $this->virtual;
    }

    public function setVirtual(bool $virtual): static
    {
        $this->virtual = $virtual;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(?int $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): static
    {
        $this->height = $height;

        return $this;
    }
}
