<?php

declare(strict_types=1);

namespace App\EventListener;

use Siganushka\OrderBundle\Entity\OrderAdjustment;
use Siganushka\OrderBundle\Event\OrderBeforeCreateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderAddAdjustmentListener implements EventSubscriberInterface
{
    public function onOrderBeforeCreate(OrderBeforeCreateEvent $event): void
    {
        $adjustment1 = new OrderAdjustment();
        $adjustment1->setAmount(random_int(1, 9) * 100);

        $adjustment2 = new OrderAdjustment();
        $adjustment2->setAmount(-random_int(1, 9) * 100);

        $order = $event->getOrder();
        $order->addAdjustment($adjustment1);
        $order->addAdjustment($adjustment2);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderBeforeCreateEvent::class => 'onOrderBeforeCreate',
        ];
    }
}
