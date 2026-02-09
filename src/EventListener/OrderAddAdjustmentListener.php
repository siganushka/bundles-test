<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\CouponDiscount;
use App\Entity\Order;
use App\Entity\RandomDiscount;
use App\Entity\ShippingFee;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(Events::prePersist, entity: Order::class)]
class OrderAddAdjustmentListener
{
    public function __invoke(Order $entity): void
    {
        $adjustments = [
            new RandomDiscount(-300),
            new CouponDiscount(-500),
            new ShippingFee(600),
        ];

        $num = random_int(0, 3);
        if (0 === $num) {
            return;
        }

        $indexs = array_rand($adjustments, $num);
        if (!\is_array($indexs)) {
            $indexs = [$indexs];
        }

        $negative = [];
        foreach ($indexs as $index) {
            $adjustment = $adjustments[$index];
            if ($adjustment->getAmount() > 0) {
                $entity->addAdjustment($adjustments[$index]);
            } else {
                $negative[] = $adjustment;
            }
        }

        foreach ($negative as $adjustment) {
            if ($entity->getTotal() <= 0) {
                break;
            }

            $entity->addAdjustment($adjustment);
        }
    }
}
