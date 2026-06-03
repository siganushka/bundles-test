<?php

declare(strict_types=1);

namespace App\Payment;

use Siganushka\ApiFactory\Alipay\PagePayUtils;
use Siganushka\TransactionBundle\Entity\Transaction;
use Siganushka\TransactionBundle\Payment\AbstractPaymentGateway;
use Siganushka\TransactionBundle\Payment\PaymentResult;
use Siganushka\TransactionBundle\Payment\PaymentResultInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AlipayPage extends AbstractPaymentGateway
{
    public function __construct(
        private readonly PagePayUtils $pagePayUtils,
        private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function pay(Transaction $transaction, array $context = []): PaymentResultInterface
    {
        $options = [
            'subject' => 'TEST',
            'out_trade_no' => $transaction->getNumber(),
            'total_amount_as_cents' => $transaction->getAmount(),
            'qr_pay_mode' => 2,
            'notify_url' => $this->urlGenerator->generate('app_transaction_alipaynotify', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        $url = $this->pagePayUtils->url($options);

        return new PaymentResult(false, $url);
    }

    public function refund(Transaction $transaction, array $context = []): array
    {
        return [];
    }

    public function notify(Transaction $transaction, array $context = []): void
    {
    }
}
