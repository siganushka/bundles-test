<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Siganushka\ProductBundle\Repository\ProductRepository as BaseProductRepository;

/**
 * @extends BaseProductRepository<Product>
 */
class ProductRepository extends BaseProductRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }
}
