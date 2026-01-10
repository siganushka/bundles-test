<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TestMappedSuperclass;
use Doctrine\Persistence\ManagerRegistry;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;

/**
 * @extends GenericEntityRepository<TestMappedSuperclass>
 */
class TestMappedSuperclassRepository extends GenericEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestMappedSuperclass::class);
    }
}
