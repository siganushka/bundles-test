<?php

declare(strict_types=1);

namespace App\Payment\Factory;

use App\Entity\PaymentTopup;
use App\Payment\Gateway\WalletPay;
use App\Repository\TopupRepository;
use App\Repository\UserRepository;
use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Enum\PaymentState;
use Siganushka\PaymentBundle\Factory\PaymentFactoryInterface;

class TopupPaymentFactory implements PaymentFactoryInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TopupRepository $topupRepository)
    {
    }

    public function createPayment(string $type, int|string $identifier, string $gateway): Payment
    {
        $user = $this->userRepository->findOneByIdentifier(WalletPay::CURRENT_USER_IDENTIFIER)
            ?? throw new \RuntimeException(\sprintf('The user "%s" does not found.', WalletPay::CURRENT_USER_IDENTIFIER));

        $topup = $this->topupRepository->find($identifier)
            ?? throw new \RuntimeException(\sprintf('The topup "%s" does not found.', $identifier));

        $fn = static fn ($_, PaymentTopup $item) => $item->getUser() === $user
            && $item->getTopup() === $topup
            && $item->getGateway() === $gateway
            && PaymentState::Pending === $item->getState();

        $payment = $user->getTopups()->findFirst($fn) ?? new PaymentTopup();
        $payment->setUser($user);
        $payment->setTopup($topup);

        return $payment;
    }

    public function supportsType(string $type): bool
    {
        return 'topup' === $type;
    }
}
