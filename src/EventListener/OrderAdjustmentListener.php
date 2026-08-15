<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Order;
use App\Entity\OrderAdjustmentCoupon;
use App\Entity\OrderAdjustmentRandom;
use App\Entity\OrderAdjustmentShipping;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(Events::prePersist, entity: Order::class)]
class OrderAdjustmentListener
{
    public function __invoke(Order $entity): void
    {
        $adjustments = [
            new OrderAdjustmentRandom(-300),
            new OrderAdjustmentCoupon(-500),
            new OrderAdjustmentShipping(600),
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
