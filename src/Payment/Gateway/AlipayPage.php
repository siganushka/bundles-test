<?php

declare(strict_types=1);

namespace App\Payment\Gateway;

use Siganushka\ApiFactory\Alipay\NotifyHandler;
use Siganushka\ApiFactory\Alipay\PagePayUtils;
use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Gateway\AbstractPaymentGateway;
use Siganushka\PaymentBundle\NotifyResult;
use Siganushka\PaymentBundle\PaymentResult;
use Siganushka\PaymentBundle\PaymentResultInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AlipayPage extends AbstractPaymentGateway
{
    public function __construct(
        private readonly UrlGeneratorInterface $generator,
        private readonly PagePayUtils $pagePayUtils,
        private readonly NotifyHandler $notifyHandler)
    {
    }

    public function pay(Payment $payment): PaymentResultInterface
    {
        $options = [
            'subject' => $payment->getTitle(),
            'out_trade_no' => $payment->getNumber(),
            'total_amount_as_cents' => $payment->getAmount(),
            'qr_pay_mode' => 2,
            'notify_url' => $this->generateNotifyUrl($this->generator),
        ];

        $url = $this->pagePayUtils->url($options);

        return new PaymentResult(false, compact('url'));
    }

    public function refund(Payment $payment): PaymentResultInterface
    {
        throw new \BadMethodCallException('Unsupported method.');
    }

    public function notify(Request $request): NotifyResult
    {
        $data = $this->notifyHandler->handle($request);
        $totalAmountAsCents = (int) ($data['total_amount'] * 100);

        return new NotifyResult(true, $data, $data['out_trade_no'], $totalAmountAsCents);
    }

    public function notifyResponse(bool $successful, ?string $message = null): Response
    {
        return \call_user_func($successful
            ? $this->notifyHandler->success(...)
            : $this->notifyHandler->fail(...));
    }
}
