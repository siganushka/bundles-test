<?php

declare(strict_types=1);

namespace App\Payment\Gateway;

use App\Entity\PaymentTopup;
use App\Repository\UserRepository;
use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Gateway\AbstractPaymentGateway;

class WalletPay extends AbstractPaymentGateway
{
    public const DETAILS_IDENTIFIER = 'identifier';

    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function supports(Payment $payment): bool
    {
        return !$payment instanceof PaymentTopup;
    }

    public function pay(Payment $payment): array
    {
        $user = $this->userRepository->find(1);
        if (!$user) {
            throw new \RuntimeException('User not found.');
        }

        $balance = $user->getBalance() - $payment->getAmount();
        if ($balance < 0) {
            throw new \RuntimeException(\sprintf('Insufficient balance (%s remaining).', $user->getBalance()));
        }

        $user->setBalance($balance);

        return [];
    }

    public function refund(Payment $payment): array
    {
        throw new \BadMethodCallException('Unsupported method.');
    }
}
