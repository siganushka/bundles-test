<?php

declare(strict_types=1);

namespace App\Payment\Gateway;

use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Gateway\AbstractPaymentGateway;
use Siganushka\PaymentBundle\PaymentResult;
use Siganushka\PaymentBundle\PaymentResultInterface;

class WalletPay extends AbstractPaymentGateway
{
    public function pay(Payment $payment, array $context = []): PaymentResultInterface
    {
        return new PaymentResult(true, ['foo' => 'bar']);
    }

    public function refund(Payment $payment, array $context = []): array
    {
        return [];
    }

    public function notify(Payment $payment, array $context = []): void
    {
    }
}
