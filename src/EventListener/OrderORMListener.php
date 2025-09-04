<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Siganushka\OrderBundle\Enum\OrderStateFlow;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsDoctrineListener(Events::prePersist)]
#[AsDoctrineListener(Events::postPersist)]
#[AsDoctrineListener(Events::preUpdate)]
#[AsDoctrineListener(Events::postUpdate)]
#[AsDoctrineListener(Events::preRemove)]
#[AsDoctrineListener(Events::postRemove)]
#[AsDoctrineListener(Events::preFlush)]
#[AsDoctrineListener(Events::onFlush)]
#[AsDoctrineListener(Events::postFlush)]
class OrderORMListener
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly WorkflowInterface $orderStateFlow,
    ) {
    }

    public function prePersist(PrePersistEventArgs $event): void
    {
        $this->logger->debug(__METHOD__.' -> '.$event->getObject()::class);
    }

    public function postPersist(PostPersistEventArgs $event): void
    {
        $this->logger->debug(__METHOD__.' -> '.$event->getObject()::class);
    }

    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $this->logger->debug(__METHOD__.' -> '.$event->getObject()::class);
    }

    public function postUpdate(PostUpdateEventArgs $event): void
    {
        $this->logger->debug(__METHOD__.' -> '.$event->getObject()::class);
    }

    public function preRemove(PreRemoveEventArgs $event): void
    {
        $this->logger->debug(__METHOD__.' -> '.$event->getObject()::class);

        $object = $event->getObject();
        if ($object instanceof Order) {
            $this->orderStateFlow->apply($object, OrderStateFlow::Cancel->value);
        }
    }

    public function postRemove(PostRemoveEventArgs $event): void
    {
        $this->logger->debug(__METHOD__.' -> '.$event->getObject()::class);
    }

    public function preFlush(): void
    {
        $this->logger->debug(__METHOD__);
    }

    public function onFlush(): void
    {
        $this->logger->debug(__METHOD__);
    }

    public function postFlush(): void
    {
        $this->logger->debug(__METHOD__);
    }
}
