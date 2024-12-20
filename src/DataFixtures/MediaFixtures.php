<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Siganushka\MediaBundle\ChannelRegistry;
use Siganushka\MediaBundle\Event\MediaSaveEvent;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MediaFixtures extends Fixture
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ChannelRegistry $channelRegistry,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $finder = new Finder();
        $filesystem = new Filesystem();

        $channels = [
            'product' => $finder->in(__DIR__.'/product'),
        ];

        $index = 0;
        foreach ($channels as $alias => $dir) {
            $channel = $this->channelRegistry->get($alias);
            foreach ($dir->sortByName()->files() as $file) {
                $target = \sprintf('%s/%s', sys_get_temp_dir(), $file->getBasename());

                $filesystem->copy($file->getPathname(), $target, true);

                $event = new MediaSaveEvent($channel, new \SplFileInfo($target));
                $this->eventDispatcher->dispatch($event);

                $media = $event->getMedia();
                if (null === $media) {
                    continue;
                }

                $manager->persist($media);
                $this->addReference(\sprintf('media-%d', $index), $media);
                ++$index;
            }
        }

        $manager->flush();
    }
}
