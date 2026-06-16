<?php

declare(strict_types=1);

namespace App\Entity;

use App\Payment\Gateway\WalletPay;
use Siganushka\PaymentBundle\Gateway\WxpayJsapi;

trait PaymentContext
{
    public function context(): array
    {
        return [
            WalletPay::DETAILS_IDENTIFIER => 'siganushka',
            WxpayJsapi::PAY_OPTIONS => [
                'openid' => 'ojARc6pRwt3nEJM5YqojEq0xHxkw', // e.g. $this->getUser()->getOpenid()
            ],
        ];
    }
}
