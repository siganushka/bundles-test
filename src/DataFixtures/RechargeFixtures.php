<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Recharge;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RechargeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $recharge0 = new Recharge();
        $recharge0->setTitle('充 100 送 20');
        $recharge0->setAmount(10000);

        $recharge1 = new Recharge();
        $recharge1->setTitle('充 300 送 100');
        $recharge1->setAmount(30000);

        $recharge2 = new Recharge();
        $recharge2->setTitle('充 500 送 300');
        $recharge2->setAmount(50000);

        $manager->persist($recharge0);
        $manager->persist($recharge1);
        $manager->persist($recharge2);
        $manager->flush();

        $this->addReference('recharge-0', $recharge0);
        $this->addReference('recharge-1', $recharge1);
        $this->addReference('recharge-2', $recharge2);
    }
}
