<?php

declare(strict_types=1);

namespace App\Payment\Gateway;

use Siganushka\ApiFactory\Alipay\PagePayUtils;
use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Gateway\AbstractPaymentGateway;
use Siganushka\PaymentBundle\PaymentResult;
use Siganushka\PaymentBundle\PaymentResultInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AlipayPage extends AbstractPaymentGateway
{
    public function __construct(
        private readonly PagePayUtils $pagePayUtils,
        private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function pay(Payment $payment, array $context = []): PaymentResultInterface
    {
        $options = [
            'subject' => 'TEST',
            'out_trade_no' => $payment->getNumber(),
            'total_amount_as_cents' => $payment->getAmount(),
            'qr_pay_mode' => 2,
            'notify_url' => $this->urlGenerator->generate('app_payment_alipaynotify', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        $url = $this->pagePayUtils->url($options);

        return new PaymentResult(false, $url);
    }

    public function refund(Payment $payment, array $context = []): array
    {
        return [];
    }

    public function notify(Payment $payment, array $context = []): void
    {
    }
}
