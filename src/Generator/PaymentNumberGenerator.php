<?php

declare(strict_types=1);

namespace App\Generator;

use Siganushka\PaymentBundle\Entity\AbstractPayment;
use Siganushka\PaymentBundle\Generator\PaymentNumberGeneratorInterface;

class PaymentNumberGenerator implements PaymentNumberGeneratorInterface
{
    public function __construct(private readonly RedisNumberGenerator $numberGenerator)
    {
    }

    public function generate(AbstractPayment $entity): string
    {
        return $this->numberGenerator->generate();
    }
}
