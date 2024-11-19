<?php

declare(strict_types=1);

namespace App\Doctrine;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Siganushka\OrderBundle\Repository\OrderAdjustmentRepository;

#[AsEntityListener(event: Events::prePersist, entity: Order::class)]
class OrderListener
{
    public function __construct(private readonly OrderAdjustmentRepository $repository)
    {
    }

    public function prePersist(Order $order): void
    {
        $adjustment1 = $this->repository->createNew();
        $adjustment1->setAmount(random_int(1, 9) * 100);

        $adjustment2 = $this->repository->createNew();
        $adjustment2->setAmount(-random_int(1, 9) * 100);

        $order->addAdjustment($adjustment1);
        $order->addAdjustment($adjustment2);
    }
}
