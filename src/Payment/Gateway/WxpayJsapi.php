<?php

declare(strict_types=1);

namespace App\Payment\Gateway;

use Siganushka\ApiFactory\Wxpay\Unifiedorder;
use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Gateway\AbstractPaymentGateway;
use Siganushka\PaymentBundle\PaymentResult;
use Siganushka\PaymentBundle\PaymentResultInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WxpayJsapi extends AbstractPaymentGateway
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Unifiedorder $unifiedorder)
    {
    }

    public function pay(Payment $payment, array $context = []): PaymentResultInterface
    {
        $options = [
            'body' => 'TEST',
            'out_trade_no' => $payment->getNumber(),
            'total_fee' => $payment->getAmount(),
            'trade_type' => 'NATIVE',
            'notify_url' => $this->urlGenerator->generate('app_payment_wxpaynotify', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        $result = $this->unifiedorder->send($options);

        return new PaymentResult(false, $result['code_url'] ?? null);
    }

    public function refund(Payment $payment, array $context = []): array
    {
        return [];
    }

    public function notify(Payment $payment, array $context = []): void
    {
    }
}
