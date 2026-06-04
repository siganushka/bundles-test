<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Order;
use App\Entity\PaymentOrder;
use App\Entity\PaymentOrderAggregate;
use App\Entity\PaymentTopup;
use App\Entity\Topup;
use Psr\Log\LoggerInterface;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Siganushka\PaymentBundle\Event\PaymentSuccessEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Workflow\Registry;

#[AsEventListener(PaymentSuccessEvent::class)]
class PaymentSuccessListener
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Registry $registry)
    {
    }

    public function __invoke(PaymentSuccessEvent $event): void
    {
        $payment = $event->getPayment();
        if ($payment instanceof PaymentTopup && $payment->getTopup()) {
            $this->handleTopup($payment->getTopup());
        } elseif ($payment instanceof PaymentOrder && $payment->getOrder()) {
            $this->handleOrder($payment->getOrder());
        } elseif ($payment instanceof PaymentOrderAggregate) {
            $payment->getOrders()->map($this->handleOrder(...));
        }
    }

    private function handleOrder(Order $entity): void
    {
        $this->registry->get($entity)->apply($entity, OrderStateTransition::Confirm->value);
    }

    private function handleTopup(Topup $entity): void
    {
        $this->logger->info(\sprintf('The topup #%s has new payment successful.', $entity->getId()));
    }
}
