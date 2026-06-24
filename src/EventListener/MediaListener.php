<?php

declare(strict_types=1);

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Siganushka\MediaBundle\Event\MediaSaveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MediaListener implements EventSubscriberInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function onMediaSave(MediaSaveEvent $event): void
    {
        $this->logger->info(__METHOD__, [
            'rule' => $event->getRule()->__toString(),
        ]);
    }

    public function onMediaSaveForTestImg(MediaSaveEvent $event): void
    {
        $this->logger->info(__METHOD__, [
            'rule' => $event->getRule()->__toString(),
        ]);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MediaSaveEvent::class => 'onMediaSave',
            MediaSaveEvent::getName('test_img') => 'onMediaSaveForTestImg',
        ];
    }
}
