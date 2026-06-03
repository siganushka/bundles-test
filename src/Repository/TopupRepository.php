<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Topup;
use Doctrine\Persistence\ManagerRegistry;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;

/**
 * @extends GenericEntityRepository<Topup>
 */
class TopupRepository extends GenericEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topup::class);
    }
}
