<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\PaymentBundle\Entity\Payment;

#[ORM\Entity]
class PaymentTopup extends Payment
{
    #[ORM\ManyToOne(inversedBy: 'payments')]
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
