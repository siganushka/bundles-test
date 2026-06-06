<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Topup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TopupFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $topup0 = new Topup();
        $topup0->setTitle('充 100 送 20');
        $topup0->setAmount(10000);
        $topup0->setBonus(2000);

        $topup1 = new Topup();
        $topup1->setTitle('充 300 送 100');
        $topup1->setAmount(30000);
        $topup1->setBonus(10000);

        $topup2 = new Topup();
        $topup2->setTitle('充 500 送 300');
        $topup2->setAmount(50000);
        $topup2->setBonus(30000);

        $manager->persist($topup0);
        $manager->persist($topup1);
        $manager->persist($topup2);
        $manager->flush();

        $this->addReference('recharge-0', $topup0);
        $this->addReference('recharge-1', $topup1);
        $this->addReference('recharge-2', $topup2);
    }
}
