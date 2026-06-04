<?php

declare(strict_types=1);

namespace App\Payment\Factory;

use App\Entity\PaymentTopup;
use App\Repository\TopupRepository;
use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Enum\PaymentState;
use Siganushka\PaymentBundle\Factory\PaymentFactoryInterface;

class TopupPaymentFactory implements PaymentFactoryInterface
{
    public function __construct(private readonly TopupRepository $topupRepository)
    {
    }

    public function createPayment(string $type, int|string $identifier, string $gateway): Payment
    {
        $entity = $this->topupRepository->find($identifier)
            ?? throw new \RuntimeException(\sprintf('The topup "%s" does not found.', $identifier));

        $fn = static fn ($_, Payment $item) => $gateway === $item->getGateway() && PaymentState::Pending === $item->getState();
        $payment = $entity->getPayments()->findFirst($fn) ?? new PaymentTopup();
        $payment->setTopup($entity);
        $payment->setGateway($gateway);

        return $payment;
    }

    public function supportsType(string $type): bool
    {
        return 'topup' === $type;
    }
}
