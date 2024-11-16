<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Siganushka\ProductBundle\Entity\Product;
use Siganushka\ProductBundle\Repository\ProductVariantRepository;

class ProductVariantFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private readonly ProductVariantRepository $productVariantRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        /** @var array<int, Product> */
        $products = [
            $this->getReference('product-0'),
            $this->getReference('product-1'),
            $this->getReference('product-2'),
            $this->getReference('product-3'),
            $this->getReference('product-4'),
            $this->getReference('product-5'),
            $this->getReference('product-6'),
            $this->getReference('product-7'),
            $this->getReference('product-8'),
            $this->getReference('product-9'),
            $this->getReference('product-10'),
        ];

        $prices = [100, 200, 300, 400, 500];
        foreach ($products as $index => $product) {
            foreach ($product->getChoices(true) as $index2 => $choice) {
                $variant = $this->productVariantRepository->createNew($product, $choice);
                $variant->setPrice($prices[array_rand($prices)]);
                $variant->setInventory(10000);
                $manager->persist($variant);

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
