<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\TransactionOrder;
use App\Repository\OrderRepository;
use Siganushka\TransactionBundle\Entity\Transaction;
use Siganushka\TransactionBundle\Enum\TransactionState;
use Siganushka\TransactionBundle\Factory\TransactionFactoryInterface;

class OrderTransactionFactory implements TransactionFactoryInterface
{
    public function __construct(private readonly OrderRepository $orderRepository)
    {
    }

    public function createTransaction(string $type, int|string $identifier, string $gateway): Transaction
    {
        $entity = $this->orderRepository->findOneBy(['number' => $identifier])
            ?? throw new \InvalidArgumentException(\sprintf('The order "%s" does not found.', $identifier));

        $fn = static fn ($_, Transaction $item) => $gateway === $item->getGateway() && TransactionState::Pending === $item->getState();
        $transaction = $entity->getTransactions()->findFirst($fn) ?? new TransactionOrder();
        $transaction->setOrder($entity);
        $transaction->setGateway($gateway);

        return $transaction;
    }

    public function supportsType(string $type): bool
    {
        return 'order' === $type;
    }
}
