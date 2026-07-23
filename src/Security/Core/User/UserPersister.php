<?php

declare(strict_types=1);

namespace App\Security\Core\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\ApiFactoryBundle\Security\Core\User\UserPersisterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserPersister implements UserPersisterInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function persist(string $userIdentifier, array $attributes): UserInterface
    {
        $user = new User();
        $user->setIdentifier($userIdentifier);
        $user->setAttributes($attributes);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
