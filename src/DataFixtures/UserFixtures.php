<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Siganushka\UserBundle\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user0 = $this->userRepository->createNew();
        $user0->setIdentifier('siganushka');
        $user0->setPassword($this->passwordHasher->hashPassword($user0, '123456'));

        $user1 = $this->userRepository->createNew();
        $user1->setIdentifier('zhangsan');
        $user1->setPassword($this->passwordHasher->hashPassword($user1, '123456'));

        $user2 = $this->userRepository->createNew();
        $user2->setIdentifier('lisi');
        $user2->setPassword($this->passwordHasher->hashPassword($user2, '123456'));
        $user2->setEnabled(false);

        $manager->persist($user0);
        $manager->persist($user1);
        $manager->persist($user2);
        $manager->flush();

        $this->addReference('user-0', $user0);
        $this->addReference('user-1', $user1);
        $this->addReference('user-2', $user2);
    }
}
