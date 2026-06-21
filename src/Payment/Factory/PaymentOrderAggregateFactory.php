<?php

declare(strict_types=1);

namespace App\Payment\Factory;

use App\Entity\PaymentOrderAggregate;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Enum\OrderState;
use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Enum\PaymentState;
use Siganushka\PaymentBundle\Factory\PaymentFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PaymentOrderAggregateFactory implements PaymentFactoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orderRepository,
        #[Autowire(param: 'siganushka_order.order_cancel_seconds')]
        private readonly int $seconds)
    {
    }

    public function createPayment(string $type, int|string $identifier, string $gateway): Payment
    {
        $number = explode(',', (string) $identifier);
        $orders = $this->orderRepository->findBy(compact('number'));

        $payment = $this->findPayment($orders, $gateway);
        if ($payment instanceof Payment) {
            return $payment;
        }

        $createdAt = [];
        foreach ($orders as $order) {
            $createdAt[] = $order->getCreatedAt();
            if (OrderState::Pending !== $order->getState()) {
                throw new \RuntimeException(\sprintf('The order "%s" has been %s.', $order->getNumber(), $order->getState()->value));
            }
        }

        /** @var \DateTimeImmutable */
        $createdAt = \count($createdAt) ? min($createdAt) : new \DateTimeImmutable();
        $expiredAt = $createdAt->modify(\sprintf('+%d seconds', $this->seconds));

        $payment = new PaymentOrderAggregate(...$orders);
        $payment->setExpiredAt($expiredAt);

        return $payment;
    }

    public function supportsType(string $type): bool
    {
        return 'order_aggregate' === $type;
    }

    private function findPayment(array $orders, string $gateway): ?Payment
    {
        $query = $this->entityManager->getRepository(PaymentOrderAggregate::class)
            ->createQueryBuilder('poa')
            ->join('poa.orders', 'o')
            ->where('o.id IN (:orders) AND poa.gateway = :gateway AND poa.state = :state')
            ->setParameter('orders', $orders)
            ->setParameter('state', PaymentState::Pending)
            ->setParameter('gateway', $gateway)
            ->setMaxResults(1)
            ->getQuery()
        ;

        /** @var Payment|null */
        $result = $query->getOneOrNullResult();

        return $result;
    }
}
