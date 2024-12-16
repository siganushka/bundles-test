<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ProductVariant;
use Doctrine\Persistence\ManagerRegistry;
use Siganushka\ProductBundle\Repository\ProductVariantRepository as BaseProductVariantRepository;

/**
 * @extends BaseProductVariantRepository<ProductVariant>
 */
class ProductVariantRepository extends BaseProductVariantRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductVariant::class);
    }
}
