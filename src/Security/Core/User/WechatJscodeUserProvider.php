<?php

declare(strict_types=1);

namespace App\Security\Core\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\AttributesBasedUserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends EntityUserProvider<User>
 * @implements AttributesBasedUserProviderInterface<User>
 */
class WechatJscodeUserProvider extends EntityUserProvider implements AttributesBasedUserProviderInterface
{
    private readonly EntityManagerInterface $entityManager;

    public function __construct(private readonly ManagerRegistry $managerRegistry)
    {
        $entityManager = $managerRegistry->getManagerForClass(User::class);
        \assert($entityManager instanceof EntityManagerInterface);

        $this->entityManager = $entityManager;

        parent::__construct($managerRegistry, User::class, 'identifier');
    }

    public function loadUserByIdentifier(string $identifier, array $attributes = []): UserInterface
    {
        try {
            return parent::loadUserByIdentifier($identifier);
        } catch (UserNotFoundException) {
            $user = new User();
            $user->setIdentifier($identifier);
            $user->setPassword($attributes['session_key']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $user;
        }
    }
}
