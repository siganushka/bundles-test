<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\PaymentBundle\Entity\AbstractPayment;
use Siganushka\PaymentBundle\Gateway\WxpayJsapi;

#[ORM\Entity]
#[ORM\InheritanceType(value: 'SINGLE_TABLE')]
class Payment extends AbstractPayment
{
    public function context(): array
    {
        return [
            // e.g. $this->getUser()->getOpenid()
            WxpayJsapi::OPTIONS_OPENID => 'ojARc6pRwt3nEJM5YqojEq0xHxkw',
        ];
    }
}
