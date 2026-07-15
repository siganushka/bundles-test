<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Order;
use App\Entity\PaymentOrder;
use App\Entity\PaymentOrderAggregate;
use App\Entity\PaymentTopup;
use Psr\Log\LoggerInterface;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Siganushka\PaymentBundle\Event\PaymentFailureEvent;
use Siganushka\PaymentBundle\Event\PaymentSuccessEvent;
use Siganushka\PaymentBundle\Event\RefundFailureEvent;
use Siganushka\PaymentBundle\Event\RefundSuccessEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Workflow\Registry;

class PaymentListener
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Registry $registry)
    {
    }

    #[AsEventListener(PaymentSuccessEvent::class)]
    public function onPaymentSuccess(PaymentSuccessEvent $event): void
    {
        $this->logger->info(__METHOD__);

        $payment = $event->getPayment();
        if ($payment instanceof PaymentTopup) {
            $this->handleTopup($payment);
        } elseif ($payment instanceof PaymentOrder && $payment->getOrder()) {
            $this->handleOrderConfirm($payment->getOrder());
        } elseif ($payment instanceof PaymentOrderAggregate) {
            $payment->getOrders()->map($this->handleOrderConfirm(...));
        }
    }

    #[AsEventListener(PaymentFailureEvent::class)]
    public function onPaymentFailure(PaymentFailureEvent $event): void
    {
        $this->logger->info(__METHOD__, [
            'number' => $event->getPayment()->getNumber(),
        ]);
    }

    #[AsEventListener(RefundSuccessEvent::class)]
    public function onRefundSuccess(RefundSuccessEvent $event): void
    {
        $this->logger->info(__METHOD__, [
            'number' => $event->getRefund()->getNumber(),
        ]);

        $payment = $event->getPayment();
        if ($payment instanceof PaymentOrder && $payment->getOrder()) {
            $this->handleOrderRefund($payment->getOrder());
        } elseif ($payment instanceof PaymentOrderAggregate) {
            $payment->getOrders()->map($this->handleOrderRefund(...));
        }
    }

    #[AsEventListener(RefundFailureEvent::class)]
    public function onRefundFailure(RefundFailureEvent $event): void
    {
        $this->logger->info(__METHOD__, [
            'number' => $event->getRefund()->getNumber(),
        ]);
    }

    private function handleOrderConfirm(Order $entity): void
    {
        $workflow = $this->registry->get($entity);
        if ($workflow->can($entity, $transitionName = OrderStateTransition::Confirm->value)) {
            $workflow->apply($entity, $transitionName);
        }
    }

    private function handleOrderRefund(Order $entity): void
    {
        $workflow = $this->registry->get($entity);
        if ($workflow->can($entity, $transitionName = OrderStateTransition::Refund->value)) {
            $workflow->apply($entity, $transitionName);
        }
    }

    private function handleTopup(PaymentTopup $payment): void
    {
        $topup = $payment->getTopup();
        $user = $payment->getUser();
        $user->setBalance($user->getBalance() + $topup->getAmount() + $topup->getBonus());
    }
}
