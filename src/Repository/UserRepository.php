<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Siganushka\UserBundle\Repository\UserRepository as BaseUserRepository;

/**
 * @extends BaseUserRepository<User>
 */
class UserRepository extends BaseUserRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
}
