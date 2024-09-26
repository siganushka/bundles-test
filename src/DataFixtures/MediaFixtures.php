<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Siganushka\MediaBundle\ChannelRegistry;
use Siganushka\MediaBundle\Event\MediaSaveEvent;
use Siganushka\ProductBundle\Media\ProductImg;
use Symfony\Component\Filesystem\Filesystem;
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
        $channelFiles = [
            ProductImg::class => glob(__DIR__.'/product/*.*'),
        ];

        $index = 0;
        foreach ($channelFiles as $alias => $files) {
            if (false === $files) {
                continue;
            }

            $channel = $this->channelRegistry->get($alias);
            foreach ($files as $file) {
                $target = \sprintf('%s/%s', sys_get_temp_dir(), pathinfo($file, \PATHINFO_BASENAME));

                $fs = new Filesystem();
                $fs->copy($file, $target);

                $event = MediaSaveEvent::createFromPath($channel, $target);
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
