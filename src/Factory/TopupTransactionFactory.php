<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\TransactionTopup;
use App\Repository\TopupRepository;
use Siganushka\TransactionBundle\Entity\Transaction;
use Siganushka\TransactionBundle\Enum\TransactionState;
use Siganushka\TransactionBundle\Factory\TransactionFactoryInterface;

class TopupTransactionFactory implements TransactionFactoryInterface
{
    public function __construct(private readonly TopupRepository $topupRepository)
    {
    }

    public function createTransaction(string $type, int|string $identifier, string $gateway): Transaction
    {
        $entity = $this->topupRepository->find($identifier)
            ?? throw new \InvalidArgumentException(\sprintf('The topup "%s" does not found.', $identifier));

        $fn = static fn ($_, Transaction $item) => $gateway === $item->getGateway() && TransactionState::Pending === $item->getState();
        $transaction = $entity->getTransactions()->findFirst($fn) ?? new TransactionTopup();
        $transaction->setTopup($entity);
        $transaction->setGateway($gateway);

        return $transaction;
    }

    public function supportsType(string $type): bool
    {
        return 'topup' === $type;
    }
}
