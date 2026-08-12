<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\ProductBundle\Entity\AbstractProductOptionValue;

#[ORM\Entity]
class ProductOptionValue extends AbstractProductOptionValue
{
}
