<?php

declare(strict_types=1);

namespace App\Payment\Gateway;

use App\Entity\PaymentTopup;
use App\Repository\UserRepository;
use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Gateway\AbstractPaymentGateway;
use Siganushka\PaymentBundle\NotifyResult;
use Siganushka\PaymentBundle\PaymentResult;
use Siganushka\PaymentBundle\PaymentResultInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function pay(Payment $payment): PaymentResultInterface
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

        return new PaymentResult(true, [
            self::DETAILS_IDENTIFIER => $user->getIdentifier(),
        ]);
    }

    public function refund(Payment $payment): PaymentResultInterface
    {
        throw new \BadMethodCallException('Unsupported method.');
    }

    public function notify(Request $request): NotifyResult
    {
        throw new \BadMethodCallException('Unsupported method.');
    }

    public function createNotifyResponse(bool $successful, ?string $message = null): Response
    {
        throw new \BadMethodCallException('Unsupported method.');
    }
}
