<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\TransactionBundle\Entity\Transaction;

#[ORM\Entity]
class TransactionOrder extends Transaction
{
    #[ORM\ManyToOne(inversedBy: 'transactions')]
    private ?Order $order = null;

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): static
    {
        $this->amount = $order?->getTotal();
        $this->order = $order;

        return $this;
    }
}
