<?php

declare(strict_types=1);

namespace App\Payment;

use Siganushka\TransactionBundle\Entity\Transaction;
use Siganushka\TransactionBundle\Payment\AbstractPaymentGateway;
use Siganushka\TransactionBundle\Payment\PaymentResult;
use Siganushka\TransactionBundle\Payment\PaymentResultInterface;

class WalletPay extends AbstractPaymentGateway
{
    public function pay(Transaction $transaction, array $context = []): PaymentResultInterface
    {
        return new PaymentResult(true, ['foo' => 'bar']);
    }

    public function refund(Transaction $transaction, array $context = []): array
    {
        return [];
    }

    public function notify(Transaction $transaction, array $context = []): void
    {
    }
}
