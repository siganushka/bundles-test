<?php

declare(strict_types=1);

namespace App\Entity;

use App\Payment\Gateway\WalletPay;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Gateway\WxpayJsapi;

#[ORM\Entity]
class PaymentOrder extends Payment
{
    #[ORM\ManyToOne(inversedBy: 'payments')]
    private ?Order $subject = null;

    public function getSubject(): ?Order
    {
        return $this->subject;
    }

    public function setSubject(?Order $subject): static
    {
        $this->title = 'Test Order';
        $this->amount = $subject?->getTotal();
        $this->subject = $subject;

        return $this;
    }

    public function resolveContext(): array
    {
        return [
            WalletPay::DETAILS_IDENTIFIER => 'siganushka.6a23cd4ba3b04',
            WxpayJsapi::PAY_OPTIONS => [
                'openid' => 'ojARc6pRwt3nEJM5YqojEq0xHxkw', // e.g. $this->getUser()->getOpenid()
            ],
        ];
    }
}
