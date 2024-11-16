<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\ProductBundle\Entity\Product as BaseProduct;

#[ORM\Entity]
class Product extends BaseProduct
{
}
