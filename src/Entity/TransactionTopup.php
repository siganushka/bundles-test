<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\TransactionBundle\Entity\Transaction;

#[ORM\Entity]
class TransactionTopup extends Transaction
{
    #[ORM\ManyToOne(inversedBy: 'transactions')]
    private ?Topup $topup = null;

    public function getTopup(): ?Topup
    {
        return $this->topup;
    }

    public function setTopup(?Topup $topup): static
    {
        $this->amount = $topup?->getAmount();
        $this->topup = $topup;

        return $this;
    }
}
