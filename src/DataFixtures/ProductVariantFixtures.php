<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductVariantFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            $this->getReference('product-0', Product::class),
            $this->getReference('product-1', Product::class),
            $this->getReference('product-2', Product::class),
            $this->getReference('product-3', Product::class),
            $this->getReference('product-4', Product::class),
            $this->getReference('product-5', Product::class),
            $this->getReference('product-6', Product::class),
            $this->getReference('product-7', Product::class),
            $this->getReference('product-8', Product::class),
            $this->getReference('product-9', Product::class),
            $this->getReference('product-10', Product::class),
        ];

        $prices = [100, 200, 300, 400, 500];
        foreach ($products as $index => $product) {
            foreach ($product->getVariants() as $index2 => $variant) {
                $variant->setPrice($prices[array_rand($prices)]);
                $variant->setStock(10000);
                $variant->setEnabled(true);

                $this->addReference(\sprintf('product-%d-variant-%d', $index, $index2), $variant);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProductFixtures::class,
        ];
    }
}
