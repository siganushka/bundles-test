<?php

declare(strict_types=1);

namespace App\Security\Core\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\ApiFactoryBundle\Security\Core\User\UserPersisterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class WechatJscodeUserPersister implements UserPersisterInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param array{ session_key: string, openid: string, unionid: string } $credentials
     */
    public function persist(array $credentials): UserInterface
    {
        $user = new User();
        $user->setIdentifier($credentials['unionid']);
        $user->setPassword($credentials['session_key']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
