<?php

declare(strict_types=1);

namespace App\Payment\Gateway;

use App\Entity\PaymentTopup;
use App\Repository\UserRepository;
use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Entity\PaymentRefund;
use Siganushka\PaymentBundle\Enum\PaymentState;
use Siganushka\PaymentBundle\Exception\PaymentFailedException;
use Siganushka\PaymentBundle\Gateway\AbstractPaymentGateway;
use Siganushka\PaymentBundle\Result\NotifyResult;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WalletPay extends AbstractPaymentGateway
{
    public const CURRENT_USER_IDENTIFIER = 'siganushka';
    public const DETAILS_IDENTIFIER = 'user_identifier';

    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function supports(Payment $payment): bool
    {
        return !$payment instanceof PaymentTopup;
    }

    public function pay(Payment $payment): array
    {
        $user = $this->userRepository->findOneByIdentifier(self::CURRENT_USER_IDENTIFIER)
            ?? throw new PaymentFailedException(\sprintf('The user "%s" does not found.', self::CURRENT_USER_IDENTIFIER));

        $balance = $user->getBalance() - $payment->getAmount();
        if ($balance < 0) {
            throw new PaymentFailedException(\sprintf('Insufficient balance (%s remaining).', $user->getBalance()));
        }

        $user->setBalance($balance);

        $details = [self::DETAILS_IDENTIFIER => $user->getIdentifier()];
        $payment->setDetails($details);
        $payment->setState(PaymentState::Succeed);

        return $details;
    }

    public function refund(Payment $payment, PaymentRefund $refund): array
    {
        $identifier = $payment->getDetails()[self::DETAILS_IDENTIFIER] ?? null;
        if (!$identifier) {
            throw new PaymentFailedException('User identifier not found.');
        }

        $user = $this->userRepository->findOneByIdentifier($identifier)
            ?? throw new \RuntimeException(\sprintf('The user "%s" does not found.', $identifier));

        $user->setBalance($user->getBalance() + $refund->getAmount());

        $details = [self::DETAILS_IDENTIFIER => $user->getIdentifier()];
        $refund->setDetails($details);
        $refund->setSuccessful(true);

        return $details;
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
