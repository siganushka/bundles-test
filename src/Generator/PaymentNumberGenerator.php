<?php

declare(strict_types=1);

namespace App\Generator;

use Siganushka\PaymentBundle\Generator\PaymentNumberGeneratorInterface;
use Siganushka\PaymentBundle\Model\PaymentInterface;

class PaymentNumberGenerator implements PaymentNumberGeneratorInterface
{
    public function __construct(private readonly RedisNumberGenerator $numberGenerator)
    {
    }

    public function generate(PaymentInterface $entity): string
    {
        return $this->numberGenerator->generate();
    }
}
