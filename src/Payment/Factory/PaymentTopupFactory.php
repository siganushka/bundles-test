<?php

declare(strict_types=1);

namespace App\Payment\Factory;

use App\Entity\PaymentTopup;
use App\Entity\Topup;
use App\Entity\User;
use App\Payment\Gateway\WalletPay;
use App\Repository\TopupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Enum\PaymentState;
use Siganushka\PaymentBundle\Factory\PaymentFactoryInterface;

class PaymentTopupFactory implements PaymentFactoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
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

        return $this->findPaymentTopup($user, $topup, $gateway) ?? new PaymentTopup($gateway, $user, $topup);
    }

    public function supportsType(string $type): bool
    {
        return 'topup' === $type;
    }

    private function findPaymentTopup(User $user, Topup $topup, string $gateway): ?PaymentTopup
    {
        return $this->entityManager->getRepository(PaymentTopup::class)
            ->findOneBy(compact('user', 'topup', 'gateway') + ['state' => PaymentState::Pending])
        ;
    }
}
