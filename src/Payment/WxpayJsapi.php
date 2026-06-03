<?php

declare(strict_types=1);

namespace App\Payment;

use Siganushka\ApiFactory\Wxpay\Unifiedorder;
use Siganushka\TransactionBundle\Entity\Transaction;
use Siganushka\TransactionBundle\Payment\AbstractPaymentGateway;
use Siganushka\TransactionBundle\Payment\PaymentResult;
use Siganushka\TransactionBundle\Payment\PaymentResultInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WxpayJsapi extends AbstractPaymentGateway
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Unifiedorder $unifiedorder)
    {
    }

    public function pay(Transaction $transaction, array $context = []): PaymentResultInterface
    {
        $options = [
            'body' => 'TEST',
            'out_trade_no' => $transaction->getNumber(),
            'total_fee' => $transaction->getAmount(),
            'trade_type' => 'NATIVE',
            'notify_url' => $this->urlGenerator->generate('app_transaction_wxpaynotify', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        $result = $this->unifiedorder->send($options);

        return new PaymentResult(false, $result['code_url'] ?? null);
    }

    public function refund(Transaction $transaction, array $context = []): array
    {
        return [];
    }

    public function notify(Transaction $transaction, array $context = []): void
    {
    }
}
