<?php

declare(strict_types=1);

namespace App\Payment\Factory;

use App\Entity\PaymentOrder;
use App\Repository\OrderRepository;
use Siganushka\OrderBundle\Enum\OrderState;
use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Enum\PaymentState;
use Siganushka\PaymentBundle\Factory\PaymentFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class OrderPaymentFactory implements PaymentFactoryInterface
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        #[Autowire(param: 'siganushka_order.order_cancel_seconds')]
        private readonly int $seconds)
    {
    }

    public function createPayment(string $type, int|string $identifier, string $gateway): Payment
    {
        $entity = $this->orderRepository->findOneBy(['number' => $identifier])
            ?? throw new \RuntimeException(\sprintf('The order "%s" does not found.', $identifier));

        $state = $entity->getState();
        if (OrderState::Pending !== $state) {
            throw new \RuntimeException(\sprintf('The order "%s" has been %s.', $identifier, $state->value));
        }

        $createdAt = $entity->getCreatedAt() ?? new \DateTimeImmutable();
        $expiredAt = $createdAt->modify(\sprintf('+%d seconds', $this->seconds));

        $fn = static fn ($_, Payment $item) => $gateway === $item->getGateway() && PaymentState::Pending === $item->getState();
        $payment = $entity->getPayments()->findFirst($fn) ?? new PaymentOrder();
        if (!$payment->getNumber()) {
            $payment->setOrder($entity);
            $payment->setExpiredAt($expiredAt);
        }

        return $payment;
    }

    public function supportsType(string $type): bool
    {
        return 'order' === $type;
    }
}
