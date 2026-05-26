<?php

declare(strict_types=1);

namespace App\Generator;

use Siganushka\TransactionBundle\Entity\Transaction;
use Siganushka\TransactionBundle\Generator\TransactionNumberGeneratorInterface;

class TransactionNumberGenerator implements TransactionNumberGeneratorInterface
{
    public function __construct(private readonly RedisNumberGenerator $numberGenerator)
    {
    }

    public function generate(Transaction $entity): string
    {
        return $this->numberGenerator->generate();
    }
}
