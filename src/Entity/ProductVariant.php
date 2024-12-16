<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductVariantRepository;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\OrderBundle\Model\OrderItemSubjectInterface;
use Siganushka\ProductBundle\Entity\ProductVariant as BaseProductVariant;

#[ORM\Entity(repositoryClass: ProductVariantRepository::class)]
class ProductVariant extends BaseProductVariant implements OrderItemSubjectInterface
{
    public function getName(): ?string
    {
        $label = $this->getChoiceLabel();
        if (!$this->product) {
            return $label;
        }

        $productName = $this->product->getName();
        if (\is_string($productName) && \is_string($label)) {
            return \sprintf('%s【%s】', $productName, $label);
        }

        return $productName;
    }
}
