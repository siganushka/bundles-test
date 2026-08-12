<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Region;
use Doctrine\Persistence\ManagerRegistry;
use Siganushka\RegionBundle\Repository\RegionRepository as BaseRegionRepository;

/**
 * @extends BaseRegionRepository<Region>
 */
class RegionRepository extends BaseRegionRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Region::class);
    }
}
