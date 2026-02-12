<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\OrderItemSubject;
use Brick\Money\Money as BrickMoney;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Money\Money as PhpMoney;

class OrderItemSubjectFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $subject0 = new OrderItemSubject();
        $subject0->setTitle('基洛夫空艇');
        $subject0->setSubtitle('出厂自带恐怖音效，令敌人闻风丧胆。');
        $subject0->setCover('https://placehold.co/100');
        $subject0->setPrice(BrickMoney::of(3000, 'CNY'));
        $subject0->setCostPrice(PhpMoney::CNY(2800));
        $subject0->setStock(100);

        $subject1 = new OrderItemSubject();
        $subject1->setTitle('天启坦克');
        $subject1->setSubtitle('自带防空系统，强大输出和肉盾。');
        $subject1->setCover('https://placehold.co/100');
        $subject1->setPrice(BrickMoney::of(2000, 'CNY'));
        $subject1->setCostPrice(PhpMoney::CNY(1800));
        $subject1->setStock(100);

        $subject2 = new OrderItemSubject();
        $subject2->setTitle('光棱坦克');
        $subject2->setSubtitle('盟军阵营顶级科技，3 级后 AOE 伤害加成。');
        $subject2->setCover('https://placehold.co/100');
        $subject2->setPrice(BrickMoney::of(1500, 'CNY'));
        $subject2->setCostPrice(PhpMoney::CNY(1400));
        $subject2->setStock(100);

        $manager->persist($subject0);
        $manager->persist($subject1);
        $manager->persist($subject2);
        $manager->flush();

        $this->addReference('subject-0', $subject0);
        $this->addReference('subject-1', $subject1);
        $this->addReference('subject-2', $subject2);
    }
}
