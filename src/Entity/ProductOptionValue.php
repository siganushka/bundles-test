<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\ProductBundle\Entity\ProductOptionValue as BaseProductOptionValue;

#[ORM\Entity]
class ProductOptionValue extends BaseProductOptionValue
{
}
