<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Media;
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
        $option0->addValue($this->productOptionValueRepository->createNew('blue', '蓝色'));
        $option0->addValue($this->productOptionValueRepository->createNew('pink', '粉色'));
        $option0->addValue($this->productOptionValueRepository->createNew('yellow', '黄色'));
        $option0->addValue($this->productOptionValueRepository->createNew('green', '绿色'));
        $option0->addValue($this->productOptionValueRepository->createNew('black', '黑色'));

        $option1 = $this->productOptionRepository->createNew('存储');
        $option1->addValue($this->productOptionValueRepository->createNew('128gb', '128GB'));
        $option1->addValue($this->productOptionValueRepository->createNew('256gb', '256GB'));
        $option1->addValue($this->productOptionValueRepository->createNew('512gb', '512GB'));

        $option2 = $this->productOptionRepository->createNew('颜色');
        $option2->addValue($this->productOptionValueRepository->createNew(null, '原色钛金属'));
        $option2->addValue($this->productOptionValueRepository->createNew(null, '蓝色钛金属'));
        $option2->addValue($this->productOptionValueRepository->createNew(null, '白色钛金属'));
        $option2->addValue($this->productOptionValueRepository->createNew(null, '黑色钛金属'));

        $option3 = $this->productOptionRepository->createNew('存储');
        $option3->addValue($this->productOptionValueRepository->createNew(null, '256GB'));
        $option3->addValue($this->productOptionValueRepository->createNew(null, '512GB'));
        $option3->addValue($this->productOptionValueRepository->createNew(null, '1TB'));

        $option4 = $this->productOptionRepository->createNew('存储');
        $option4->addValue($this->productOptionValueRepository->createNew(null, '8GB+256GB'));
        $option4->addValue($this->productOptionValueRepository->createNew(null, '8GB+512GB'));
        $option4->addValue($this->productOptionValueRepository->createNew(null, '12GB+512GB'));

        $option5 = $this->productOptionRepository->createNew('颜色');
        $option5->addValue($this->productOptionValueRepository->createNew(null, '秘矿紫'));
        $option5->addValue($this->productOptionValueRepository->createNew(null, '浅珀黄'));
        $option5->addValue($this->productOptionValueRepository->createNew(null, '水墨黑'));
        $option5->addValue($this->productOptionValueRepository->createNew(null, '雅岩灰'));

        $option6 = $this->productOptionRepository->createNew('存储');
        $option6->addValue($this->productOptionValueRepository->createNew(null, '12GB+256GB'));
        $option6->addValue($this->productOptionValueRepository->createNew(null, '12GB+512GB'));
        $option6->addValue($this->productOptionValueRepository->createNew(null, '12GB+1TB'));

        $option7 = $this->productOptionRepository->createNew('颜色');
        $option7->addValue($this->productOptionValueRepository->createNew(null, '钛灰'));
        $option7->addValue($this->productOptionValueRepository->createNew(null, '钛黑'));
        $option7->addValue($this->productOptionValueRepository->createNew(null, '钛暮紫'));
        $option7->addValue($this->productOptionValueRepository->createNew(null, '钛羽黄'));

        $option8 = $this->productOptionRepository->createNew('尺码');
        $option8->addValue($this->productOptionValueRepository->createNew(null, '25'));
        $option8->addValue($this->productOptionValueRepository->createNew(null, '26'));
        $option8->addValue($this->productOptionValueRepository->createNew(null, '27'));
        $option8->addValue($this->productOptionValueRepository->createNew(null, '28'));
        $option8->addValue($this->productOptionValueRepository->createNew(null, '29'));
        $option8->addValue($this->productOptionValueRepository->createNew(null, '30'));
        $option8->addValue($this->productOptionValueRepository->createNew(null, '31'));
        $option8->addValue($this->productOptionValueRepository->createNew(null, '32'));

        $option9 = $this->productOptionRepository->createNew('尺码');
        $option9->addValue($this->productOptionValueRepository->createNew(null, 'M'));
        $option9->addValue($this->productOptionValueRepository->createNew(null, 'L'));
        $option9->addValue($this->productOptionValueRepository->createNew(null, 'XL'));
        $option9->addValue($this->productOptionValueRepository->createNew(null, '2XL'));
        $option9->addValue($this->productOptionValueRepository->createNew(null, '3XL'));

        $option10 = $this->productOptionRepository->createNew('辣度');
        $option10->addValue($this->productOptionValueRepository->createNew(null, '不辣', $this->getReference('media-10', Media::class)));
        $option10->addValue($this->productOptionValueRepository->createNew(null, '微辣', $this->getReference('media-11', Media::class)));
        $option10->addValue($this->productOptionValueRepository->createNew(null, '中辣', $this->getReference('media-12', Media::class)));
        $option10->addValue($this->productOptionValueRepository->createNew(null, '特辣', $this->getReference('media-13', Media::class)));
        $option10->addValue($this->productOptionValueRepository->createNew(null, '变态辣', $this->getReference('media-14', Media::class)));

        $product0 = $this->productRepository->createNew();
        $product0->setName('苹果 iPhone 15');
        $product0->setImg($this->getReference('media-0', Media::class));
        $product0->addOption($option0);
        $product0->addOption($option1);

        $product1 = $this->productRepository->createNew();
        $product1->setName('苹果 iPhone 15 Plus');
        $product1->setImg($this->getReference('media-0', Media::class));
        $product1->addOption(clone $option0);
        $product1->addOption(clone $option1);

        $product2 = $this->productRepository->createNew();
        $product2->setName('苹果 iPhone 15 Pro');
        $product2->setImg($this->getReference('media-1', Media::class));
        $product2->addOption($option2);
        $product2->addOption($option3);

        $product3 = $this->productRepository->createNew();
        $product3->setName('苹果 iPhone 15 Pro Max');
        $product3->setImg($this->getReference('media-1', Media::class));
        $product3->addOption(clone $option2);
        $product3->addOption(clone $option3);

        $product4 = $this->productRepository->createNew();
        $product4->setName('三星 S24');
        $product4->setImg($this->getReference('media-2', Media::class));
        $product4->addOption($option4);
        $product4->addOption($option5);

        $product5 = $this->productRepository->createNew();
        $product5->setName('三星 S24+');
        $product5->setImg($this->getReference('media-2', Media::class));
        $product5->addOption(clone $option4);
        $product5->addOption(clone $option5);

        $product6 = $this->productRepository->createNew();
        $product6->setName('三星 S24 Ultra');
        $product6->setImg($this->getReference('media-3', Media::class));
        $product6->addOption($option6);
        $product6->addOption($option7);

        $product7 = $this->productRepository->createNew();
        $product7->setName('耐克幼童易穿脱运动童鞋');
        $product7->setImg($this->getReference('media-4', Media::class));
        $product7->addOption($option8);

        $product8 = $this->productRepository->createNew();
        $product8->setName('新品春季时尚卫衣');
        $product8->setImg($this->getReference('media-5', Media::class));
        $product8->addOption($option9);

        $product9 = $this->productRepository->createNew();
        $product9->setName('正宗陕西油泼面');
        $product9->setImg($this->getReference('media-6', Media::class));
        $product9->addOption($option10);

        $product10 = $this->productRepository->createNew();
        $product10->setName('迪卡侬保冷野餐包');
        $product10->setImg($this->getReference('media-7', Media::class));

        $product11 = $this->productRepository->createNew();
        $product11->setName('Apple Music 包年套餐礼品卡');
        $product11->setImg($this->getReference('media-8', Media::class));
        $product11->setVirtual(true);

        $product12 = $this->productRepository->createNew();
        $product12->setName('Windows 11 专业版序列号');
        $product12->setImg($this->getReference('media-9', Media::class));
        $product12->setVirtual(true);

        $manager->persist($product0);
        $manager->persist($product1);
        $manager->persist($product2);
        $manager->persist($product3);
        $manager->persist($product4);
        $manager->persist($product5);
        $manager->persist($product6);
        $manager->persist($product7);
        $manager->persist($product8);
        $manager->persist($product9);
        $manager->persist($product10);
        $manager->persist($product11);
        $manager->persist($product12);
        $manager->flush();

        $this->addReference('product-0', $product0);
        $this->addReference('product-1', $product1);
        $this->addReference('product-2', $product2);
        $this->addReference('product-3', $product3);
        $this->addReference('product-4', $product4);
        $this->addReference('product-5', $product5);
        $this->addReference('product-6', $product6);
        $this->addReference('product-7', $product7);
        $this->addReference('product-8', $product8);
        $this->addReference('product-9', $product9);
        $this->addReference('product-10', $product10);
        $this->addReference('product-11', $product11);
        $this->addReference('product-12', $product12);
    }

    public function getDependencies(): array
    {
        return [
            MediaFixtures::class,
        ];
    }
}
