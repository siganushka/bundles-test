<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Media;
use App\Entity\Product;
use App\Entity\ProductVariant;
use App\Repository\ProductRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Siganushka\ProductBundle\Repository\ProductOptionRepository;
use Siganushka\ProductBundle\Repository\ProductOptionValueRepository;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly ProductOptionRepository $productOptionRepository,
        private readonly ProductOptionValueRepository $productOptionValueRepository)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $option0 = $this->productOptionRepository->createNew('颜色');
        $option0->addValue($this->productOptionValueRepository->createNew('lavender', '薰衣草紫色', $this->getReference('media-product-option-value-3', Media::class)));
        $option0->addValue($this->productOptionValueRepository->createNew('sage', '鼠尾草绿色', $this->getReference('media-product-option-value-4', Media::class)));
        $option0->addValue($this->productOptionValueRepository->createNew('mistblue', '青雾蓝色', $this->getReference('media-product-option-value-5', Media::class)));
        $option0->addValue($this->productOptionValueRepository->createNew('white', '白色', $this->getReference('media-product-option-value-6', Media::class)));
        $option0->addValue($this->productOptionValueRepository->createNew('black', '黑色', $this->getReference('media-product-option-value-7', Media::class)));

        $option1 = $this->productOptionRepository->createNew('存储');
        $option1->addValue($this->productOptionValueRepository->createNew(null, '256GB'));
        $option1->addValue($this->productOptionValueRepository->createNew(null, '512GB'));

        $option2 = $this->productOptionRepository->createNew('颜色');
        $option2->addValue($this->productOptionValueRepository->createNew('silver', '银色', $this->getReference('media-product-option-value-0', Media::class)));
        $option2->addValue($this->productOptionValueRepository->createNew('cosmicorange', '星宇橙色', $this->getReference('media-product-option-value-1', Media::class)));
        $option2->addValue($this->productOptionValueRepository->createNew('deepblue', '深蓝色', $this->getReference('media-product-option-value-2', Media::class)));

        $option3 = $this->productOptionRepository->createNew('存储');
        $option3->addValue($this->productOptionValueRepository->createNew(null, '256GB'));
        $option3->addValue($this->productOptionValueRepository->createNew(null, '512GB'));
        $option3->addValue($this->productOptionValueRepository->createNew(null, '1TB'));
        $option3->addValue($this->productOptionValueRepository->createNew(null, '2TB'));

        $option4 = $this->productOptionRepository->createNew('尺码');
        $option4->addValue($this->productOptionValueRepository->createNew(null, '25'));
        $option4->addValue($this->productOptionValueRepository->createNew(null, '26'));
        $option4->addValue($this->productOptionValueRepository->createNew(null, '27'));
        $option4->addValue($this->productOptionValueRepository->createNew(null, '28'));
        $option4->addValue($this->productOptionValueRepository->createNew(null, '29'));
        $option4->addValue($this->productOptionValueRepository->createNew(null, '30'));
        $option4->addValue($this->productOptionValueRepository->createNew(null, '31'));
        $option4->addValue($this->productOptionValueRepository->createNew(null, '32'));

        $option5 = $this->productOptionRepository->createNew('尺码');
        $option5->addValue($this->productOptionValueRepository->createNew(null, 'M'));
        $option5->addValue($this->productOptionValueRepository->createNew(null, 'L'));
        $option5->addValue($this->productOptionValueRepository->createNew(null, 'XL'));
        $option5->addValue($this->productOptionValueRepository->createNew(null, '2XL'));
        $option5->addValue($this->productOptionValueRepository->createNew(null, '3XL'));

        $option6 = $this->productOptionRepository->createNew('辣度');
        $option6->addValue($this->productOptionValueRepository->createNew(null, '不辣', $this->getReference('media-product-option-value-8', Media::class)));
        $option6->addValue($this->productOptionValueRepository->createNew(null, '微辣', $this->getReference('media-product-option-value-9', Media::class)));
        $option6->addValue($this->productOptionValueRepository->createNew(null, '中辣', $this->getReference('media-product-option-value-10', Media::class)));
        $option6->addValue($this->productOptionValueRepository->createNew(null, '特辣', $this->getReference('media-product-option-value-11', Media::class)));
        $option6->addValue($this->productOptionValueRepository->createNew(null, '变态辣', $this->getReference('media-product-option-value-12', Media::class)));

        $product0 = $this->productRepository->createNew();
        $product0->setName('Apple iPhone 17');
        $product0->setImg($this->getReference('media-product-0', Media::class));
        $product0->addOption($option0);
        $product0->addOption($option1);

        $product1 = $this->productRepository->createNew();
        $product1->setName('Apple iPhone 17 Pro');
        $product1->setImg($this->getReference('media-product-1', Media::class));
        $product1->addOption($option2);
        $product1->addOption($option3);

        $product2 = $this->productRepository->createNew();
        $product2->setName('Apple iPhone 17 Pro Max');
        $product2->setImg($this->getReference('media-product-1', Media::class));
        $product2->addOption(clone $option2);
        $product2->addOption(clone $option3);

        $product3 = $this->productRepository->createNew();
        $product3->setName('耐克幼童易穿脱运动童鞋');
        $product3->setImg($this->getReference('media-product-2', Media::class));
        $product3->addOption($option4);

        $product4 = $this->productRepository->createNew();
        $product4->setName('新品春季时尚卫衣');
        $product4->setImg($this->getReference('media-product-3', Media::class));
        $product4->addOption($option5);

        $product5 = $this->productRepository->createNew();
        $product5->setName('正宗陕西油泼面');
        $product5->setImg($this->getReference('media-product-4', Media::class));
        $product5->addOption($option6);

        $product6 = $this->productRepository->createNew();
        $product6->setName('迪卡侬保冷野餐包');
        $product6->setImg($this->getReference('media-product-5', Media::class));

        /** @var array<int, Product> */
        $products = [$product0, $product1, $product2, $product3, $product4, $product5, $product6];

        $prices = [100, 200, 300, 400, 500];
        foreach ($products as $index => $product) {
            foreach ($product->generateChoices() as $index2 => $choice) {
                $variant = new ProductVariant($choice);
                $variant->setPrice($prices[array_rand($prices)]);
                $variant->setStock(10000);
                $variant->setEnabled(true);
                $product->addVariant($variant);

                $this->addReference(\sprintf('product-%d-variant-%d', $index, $index2), $variant);
            }
        }

        $manager->persist($product0);
        $manager->persist($product1);
        $manager->persist($product2);
        $manager->persist($product3);
        $manager->persist($product4);
        $manager->persist($product5);
        $manager->persist($product6);
        $manager->flush();

        array_walk($products, fn (Product $item, int $index) => $this->addReference(\sprintf('product-%d', $index), $item));
    }

    public function getDependencies(): array
    {
        return [
            MediaFixtures::class,
        ];
    }
}
