<?php

declare(strict_types=1);

namespace App\Payment\Factory;

use App\Entity\Order;
use App\Entity\PaymentOrder;
use App\Entity\PaymentOrderAggregate;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Enum\OrderState;
use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Enum\PaymentState;
use Siganushka\PaymentBundle\Factory\PaymentFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PaymentOrderFactory implements PaymentFactoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orderRepository,
        #[Autowire(param: 'siganushka_order.order_expire_seconds')]
        private readonly int $seconds)
    {
    }

    public function createPayment(string $type, int|string $identifier, string $gateway): Payment
    {
        $orders = $created = [];
        foreach (explode(',', (string) $identifier) as $number) {
            $order = $this->orderRepository->findOneBy(compact('number'))
                ?? throw new \RuntimeException(\sprintf('The order "%s" does not found.', $number));

            $state = $order->getState();
            if (OrderState::Pending !== $order->getState()) {
                throw new \RuntimeException(\sprintf('The order "%s" has been %s.', $number, $state->value));
            }

            $orders[] = $order;
            $created[] = $order->getCreatedAt();
        }

        $createdAt = min($created) ?? new \DateTimeImmutable();
        $expiredAt = $createdAt->modify(\sprintf('+%d seconds', $this->seconds));

        return 1 === \count($orders)
            ? ($this->findPaymentOrder($orders[0], $gateway) ?? new PaymentOrder($gateway, $orders[0], $expiredAt))
            : ($this->findPaymentOrderAggregate($orders, $gateway) ?? new PaymentOrderAggregate($gateway, $orders, $expiredAt));
    }

    public function supportsType(string $type): bool
    {
        return 'order' === $type;
    }

    private function findPaymentOrder(Order $order, string $gateway): ?PaymentOrder
    {
        return $this->entityManager->getRepository(PaymentOrder::class)
            ->findOneBy(compact('order', 'gateway') + ['state' => PaymentState::Pending])
        ;
    }

    private function findPaymentOrderAggregate(array $orders, string $gateway): ?PaymentOrderAggregate
    {
        $query = $this->entityManager->getRepository(PaymentOrderAggregate::class)
            ->createQueryBuilder('poa')
            ->join('poa.orders', 'o')
            ->where('o.id IN (:orders) AND poa.gateway = :gateway AND poa.state = :state')
            ->setParameter('orders', $orders)
            ->setParameter('gateway', $gateway)
            ->setParameter('state', PaymentState::Pending)
            ->setMaxResults(1)
            ->getQuery()
        ;

        /** @var PaymentOrderAggregate|null */
        $result = $query->getOneOrNullResult();

        return $result;
    }
}
