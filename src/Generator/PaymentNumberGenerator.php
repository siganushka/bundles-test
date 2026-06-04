<?php

declare(strict_types=1);

namespace App\Generator;

use Siganushka\PaymentBundle\Entity\Payment;
use Siganushka\PaymentBundle\Generator\PaymentNumberGeneratorInterface;

class PaymentNumberGenerator implements PaymentNumberGeneratorInterface
{
    public function __construct(private readonly RedisNumberGenerator $numberGenerator)
    {
    }

    public function generate(Payment $entity): string
    {
        return $this->numberGenerator->generate();
    }
}
