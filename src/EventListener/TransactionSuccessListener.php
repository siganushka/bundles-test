<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Order;
use App\Entity\Topup;
use App\Entity\TransactionOrder;
use App\Entity\TransactionOrderAggregate;
use App\Entity\TransactionTopup;
use Psr\Log\LoggerInterface;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Siganushka\TransactionBundle\Event\TransactionSuccessEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Workflow\Registry;

#[AsEventListener(TransactionSuccessEvent::class)]
class TransactionSuccessListener
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Registry $registry)
    {
    }

    public function __invoke(TransactionSuccessEvent $event): void
    {
        $transaction = $event->getTransaction();
        if ($transaction instanceof TransactionTopup && $entity = $transaction->getTopup()) {
            $this->handleTopup($entity);
        } elseif ($transaction instanceof TransactionOrder && $entity = $transaction->getOrder()) {
            $this->handleOrder($entity);
        } elseif ($transaction instanceof TransactionOrderAggregate) {
            $transaction->getOrders()->map($this->handleOrder(...));
        }
    }

    private function handleOrder(Order $entity): void
    {
        try {
            $this->registry->get($entity)->apply($entity, OrderStateTransition::Confirm->value);
        } catch (\Throwable $th) {
            $this->logger->error($th->getMessage());
        }
    }

    private function handleTopup(Topup $entity): void
    {
        $this->logger->info(\sprintf('The topup #%s has new successed transaction.', $entity->getId()));
    }
}
