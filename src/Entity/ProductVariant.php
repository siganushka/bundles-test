<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\OrderBundle\Model\OrderItemSubjectInterface;
use Siganushka\ProductBundle\Entity\ProductVariant as BaseProductVariant;

#[ORM\Entity]
class ProductVariant extends BaseProductVariant implements OrderItemSubjectInterface
{
    public function getName(): ?string
    {
        return $this->product?->getName();
    }
}
