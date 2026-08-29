<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Order;
use Psr\Log\LoggerInterface;
use Siganushka\GenericBundle\Event\EntityBeforeCreateEvent;
use Siganushka\GenericBundle\Event\EntityBeforeDeleteEvent;
use Siganushka\GenericBundle\Event\EntityBeforeUpdateEvent;
use Siganushka\GenericBundle\Event\EntityCreatedEvent;
use Siganushka\GenericBundle\Event\EntityDeletedEvent;
use Siganushka\GenericBundle\Event\EntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderEventListener implements EventSubscriberInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function onBeforeCreate(EntityBeforeCreateEvent $event): void
    {
        $this->logger->debug(__METHOD__.' -> '.$event->getEntity()::class);
    }

    public function onBeforeUpdate(EntityBeforeUpdateEvent $event): void
    {
        $this->logger->debug(__METHOD__.' -> '.$event->getEntity()::class);
    }

    public function onBeforeDelete(EntityBeforeDeleteEvent $event): void
    {
        $this->logger->debug(__METHOD__.' -> '.$event->getEntity()::class);
    }

    public function onCreated(EntityCreatedEvent $event): void
    {
        $this->logger->debug(__METHOD__.' -> '.$event->getEntity()::class);
    }

    public function onUpdated(EntityUpdatedEvent $event): void
    {
        $this->logger->debug(__METHOD__.' -> '.$event->getEntity()::class);
    }

    public function onDeleted(EntityDeletedEvent $event): void
    {
        $this->logger->debug(__METHOD__.' -> '.$event->getEntity()::class);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EntityBeforeCreateEvent::getName(Order::class) => 'onBeforeCreate',
            EntityBeforeUpdateEvent::getName(Order::class) => 'onBeforeUpdate',
            EntityBeforeDeleteEvent::getName(Order::class) => 'onBeforeDelete',
            EntityCreatedEvent::getName(Order::class) => 'onCreated',
            EntityUpdatedEvent::getName(Order::class) => 'onUpdated',
            EntityDeletedEvent::getName(Order::class) => 'onDeleted',
        ];
    }
}
