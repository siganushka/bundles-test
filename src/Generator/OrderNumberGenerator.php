<?php

declare(strict_types=1);

namespace App\Generator;

use Siganushka\OrderBundle\Entity\AbstractOrder;
use Siganushka\OrderBundle\Generator\OrderNumberGeneratorInterface;

class OrderNumberGenerator implements OrderNumberGeneratorInterface
{
    public function __construct(private readonly RedisNumberGenerator $numberGenerator)
    {
    }

    public function generate(AbstractOrder $entity): string
    {
        return $this->numberGenerator->generate();
    }
}
