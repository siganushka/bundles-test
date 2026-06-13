<?php

declare(strict_types=1);

namespace App\Payment\Gateway;

use App\Entity\PaymentTopup;
use App\Repository\UserRepository;
use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Entity\PaymentRefund;
use Siganushka\PaymentBundle\Gateway\AbstractPaymentGateway;
use Siganushka\PaymentBundle\Result\NotifyResult;
use Siganushka\PaymentBundle\Result\PaymentResult;
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

    public function pay(Payment $payment): PaymentResult
    {
        $identifier = $payment->resolveContext()[self::DETAILS_IDENTIFIER] ?? null;
        if (!$identifier) {
            throw new \RuntimeException('User identifier not found.');
        }

        $user = $this->userRepository->findOneByIdentifier($identifier);
        if (!$user) {
            throw new \RuntimeException('User not found.');
        }

        $balance = $user->getBalance() - $payment->getAmount();
        if ($balance < 0) {
            throw new \RuntimeException(\sprintf('Insufficient balance (%s remaining).', $user->getBalance()));
        }

        $user->setBalance($balance);

        return new PaymentResult(null, [self::DETAILS_IDENTIFIER => $user->getIdentifier()], true);
    }

    public function refund(Payment $payment, PaymentRefund $refund): PaymentResult
    {
        $user = $this->userRepository->find(1);
        if (!$user) {
            throw new \RuntimeException('User not found.');
        }

        $user->setBalance($user->getBalance() + $refund->getAmount());

        return new PaymentResult(null, [self::DETAILS_IDENTIFIER => $user->getIdentifier()], true);
    }

    public function notify(Request $request): NotifyResult
    {
        throw new \BadMethodCallException('Unsupported method.');
    }

    public function notifyResponse(bool $successful, ?string $message = null): Response
    {
        throw new \BadMethodCallException('Unsupported method.');
    }
}
