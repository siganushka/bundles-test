<?php

declare(strict_types=1);

namespace App\Entity;

use Siganushka\PaymentBundle\Gateway\WxpayJsapi;

trait PaymentContext
{
    public function context(): array
    {
        return [
            // e.g. $this->getUser()->getOpenid()
            WxpayJsapi::OPTIONS_OPENID => 'ojARc6pRwt3nEJM5YqojEq0xHxkw',
        ];
    }
}
