<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\CouponDiscount;
use App\Entity\RandomDiscount;
use App\Entity\ShippingFee;
use Siganushka\OrderBundle\Event\OrderBeforeCreateEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: OrderBeforeCreateEvent::class)]
class OrderAddAdjustmentListener
{
    public function __invoke(OrderBeforeCreateEvent $event): void
    {
        $adjustment1 = new ShippingFee();
        $adjustment1->setAmount(random_int(1, 9) * 100);

        $adjustment2 = new RandomDiscount();
        $adjustment2->setAmount(-random_int(1, 3) * 100);

        $adjustment3 = new CouponDiscount();
        $adjustment3->setAmount(-random_int(1, 9) * 100);

        $entity = $event->getOrder();
        $entity->addAdjustment($adjustment1);
        $entity->addAdjustment($adjustment2);
        $entity->addAdjustment($adjustment3);
    }
}
