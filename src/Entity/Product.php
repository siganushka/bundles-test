<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\ProductBundle\Entity\Product as BaseProduct;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product extends BaseProduct
{
}
