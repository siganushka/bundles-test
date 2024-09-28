<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Region;
use Doctrine\Persistence\ManagerRegistry;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;

/**
 * @extends GenericEntityRepository<Region>
 *
 * @method Region      createNew(...$args)
 * @method Region|null find($id, $lockMode = null, $lockVersion = null)
 * @method Region|null findOneBy(array $criteria, array $orderBy = null)
 * @method Region[]    findAll()
 * @method Region[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegionRepository extends GenericEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Region::class);
    }
}
