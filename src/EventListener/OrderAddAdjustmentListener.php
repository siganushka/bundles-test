<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\CouponDiscount;
use App\Entity\Order;
use App\Entity\RandomDiscount;
use App\Entity\ShippingFee;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

// #[AsEntityListener(Events::prePersist, entity: Order::class)]
class OrderAddAdjustmentListener
{
    public function __invoke(Order $entity): void
    {
        $adjustments = [
            (new ShippingFee())->setAmount(600),
            (new RandomDiscount())->setAmount(-300),
            (new CouponDiscount())->setAmount(-500),
        ];

        $num = random_int(0, 3);
        if (0 === $num) {
            return;
        }

        $indexs = array_rand($adjustments, $num);
        if (!\is_array($indexs)) {
            $indexs = [$indexs];
        }

        foreach ($indexs as $index) {
            $entity->addAdjustment($adjustments[$index]);
        }
    }
}
