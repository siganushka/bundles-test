<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\PaymentBundle\Entity\Payment;

#[ORM\Entity]
class PaymentTopup extends Payment
{
    use PaymentContext;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    private ?Topup $subject = null;

    public function getSubject(): ?Topup
    {
        return $this->subject;
    }

    public function setSubject(?Topup $subject): static
    {
        $this->title = 'Test Topup';
        $this->amount = $subject?->getAmount();
        $this->subject = $subject;

        return $this;
    }
}
