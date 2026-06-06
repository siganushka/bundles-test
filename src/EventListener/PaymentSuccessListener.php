<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Order;
use App\Entity\PaymentOrder;
use App\Entity\PaymentOrderAggregate;
use App\Entity\PaymentTopup;
use App\Payment\Gateway\WalletPay;
use App\Repository\UserRepository;
use Siganushka\OrderBundle\Enum\OrderStateTransition;
use Siganushka\PaymentBundle\Event\PaymentSuccessEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Workflow\Registry;

#[AsEventListener(PaymentSuccessEvent::class)]
class PaymentSuccessListener
{
    public const TOPUP_IDENTIFIER = 'identifier';

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly Registry $registry)
    {
    }

    public function __invoke(PaymentSuccessEvent $event): void
    {
        $payment = $event->getPayment();
        if ($payment instanceof PaymentTopup) {
            $this->handleTopup($payment);
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

    private function handleTopup(PaymentTopup $payment): void
    {
        $topup = $payment->getTopup();
        if (!$topup) {
            return;
        }

        $identifier = $payment->getDetails()[WalletPay::DETAILS_IDENTIFIER] ?? null;
        if (\is_string($identifier) && $user = $this->userRepository->findOneByIdentifier($identifier)) {
            $user->setBalance($user->getBalance() + $topup->getAmount() + $topup->getBonus());
        }
    }
}
