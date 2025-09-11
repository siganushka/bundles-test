<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Siganushka\MediaBundle\MediaManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class MediaFixtures extends Fixture
{
    public function __construct(private readonly MediaManagerInterface $mediaManager)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $finder = new Finder();
        $filesystem = new Filesystem();

        $rules = [
            'product_img' => $finder->in(__DIR__.'/product'),
        ];

        $index = 0;
        foreach ($rules as $ruleAlias => $dir) {
            foreach ($dir->sortByName()->files() as $file) {
                $target = \sprintf('%s/%s', sys_get_temp_dir(), $file->getBasename());
                $filesystem->copy($file->getPathname(), $target, true);

                $media = $this->mediaManager->save($ruleAlias, $target);
                $manager->persist($media);

                $this->addReference(\sprintf('media-%d', $index), $media);
                ++$index;
            }
        }

        $manager->flush();
    }
}
