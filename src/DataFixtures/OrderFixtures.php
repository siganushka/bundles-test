<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Entity\OrderItem;
use Siganushka\ProductBundle\Entity\ProductVariant;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $subject0 = $this->getReference('product-0-variant-0', ProductVariant::class);
        $subject1 = $this->getReference('product-0-variant-1', ProductVariant::class);
        $subject2 = $this->getReference('product-0-variant-2', ProductVariant::class);
        $subject3 = $this->getReference('product-8-variant-0', ProductVariant::class);
        $subject4 = $this->getReference('product-10-variant-0', ProductVariant::class);

        $order0 = new Order();
        $order0->addItem(new OrderItem($subject0, 2));
        $order0->addItem(new OrderItem($subject1, 2));
        $order0->addItem(new OrderItem($subject2, 2));

        $order1 = new Order();
        $order1->addItem(new OrderItem($subject3, 2));

        $order2 = new Order();
        $order2->addItem(new OrderItem($subject4, 2));

        $manager->persist($order0);
        $manager->persist($order1);
        $manager->persist($order2);
        $manager->flush();

        $this->addReference('order-0', $order0);
        $this->addReference('order-1', $order1);
        $this->addReference('order-2', $order2);
    }

    public function getDependencies(): array
    {
        return [
            ProductVariantFixtures::class,
        ];
    }
}
