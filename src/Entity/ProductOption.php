<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\ProductBundle\Entity\ProductOption as BaseProductOption;

#[ORM\Entity]
class ProductOption extends BaseProductOption
{
}
