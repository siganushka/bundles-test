<?php

declare(strict_types=1);

namespace App\Payment\Gateway;

use Siganushka\ApiFactory\Wxpay\NotifyHandler;
use Siganushka\ApiFactory\Wxpay\Unifiedorder;
use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Gateway\AbstractPaymentGateway;
use Siganushka\PaymentBundle\NotifyResult;
use Siganushka\PaymentBundle\PaymentResult;
use Siganushka\PaymentBundle\PaymentResultInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WxpayJsapi extends AbstractPaymentGateway
{
    public function __construct(
        private readonly UrlGeneratorInterface $generator,
        private readonly Unifiedorder $unifiedorder,
        private readonly NotifyHandler $notifyHandler)
    {
    }

    public function pay(Payment $payment): PaymentResultInterface
    {
        $options = [
            'body' => $payment->getTitle(),
            'out_trade_no' => $payment->getNumber(),
            'total_fee' => $payment->getAmount(),
            'trade_type' => 'NATIVE',
            'notify_url' => $this->generateNotifyUrl($this->generator),
        ];

        $result = $this->unifiedorder->send($options);
        // Only reserve code_url to response.
        $data = array_filter($result, static fn (string $key) => 'code_url' === $key, \ARRAY_FILTER_USE_KEY);

        return new PaymentResult(false, $data);
    }

    public function refund(Payment $payment): PaymentResultInterface
    {
        throw new \BadMethodCallException('Unsupported method.');
    }

    public function notify(Request $request): NotifyResult
    {
        $data = $this->notifyHandler->handle($request);

        return new NotifyResult(true, $data, $data['out_trade_no'], $data['total_fee']);
    }

    public function notifyResponse(bool $successful, ?string $message = null): Response
    {
        return \call_user_func($successful
            ? $this->notifyHandler->success(...)
            : $this->notifyHandler->fail(...), $message);
    }
}
