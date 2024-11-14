<?php

declare(strict_types=1);

namespace App\Message;

final class OrderCancelledMessage
{
    public function __construct(private readonly string $number)
    {
    }

    public function getNumber(): string
    {
        return $this->number;
    }
}
