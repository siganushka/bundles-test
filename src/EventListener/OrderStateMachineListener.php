<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Order;
use Psr\Log\LoggerInterface;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Siganushka\PaymentBundle\Entity\PaymentRefund;
use Siganushka\PaymentBundle\PaymentManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;

class OrderStateMachineListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly PaymentManagerInterface $paymentManager)
    {
    }

    public function onCancel(TransitionEvent $event): void
    {
        $subject = $event->getSubject();
        if (!$subject instanceof Order) {
            return;
        }

        $payment = $subject->getCurrentPayment();
        if (!$payment) {
            return;
        }

        $refundable = $payment->getRefundableAmount();
        if (null === $refundable || $refundable <= 0) {
            return;
        }

        $refund = PaymentRefund::createFromPayment($payment);
        $refund->setAmount($refundable);

        try {
            $this->paymentManager->refund($payment, $refund);
        } catch (\Throwable $th) {
            $error = $th->getMessage();
            $this->logger->error(__METHOD__, compact('error'));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TransitionEvent::getName('order', OrderStateTransition::Cancel->value) => 'onCancel',
        ];
    }
}
