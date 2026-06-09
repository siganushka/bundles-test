<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\PaymentBundle\Entity\Payment;

#[ORM\Entity]
class PaymentOrder extends Payment
{
    #[ORM\ManyToOne(inversedBy: 'payments')]
    private ?Order $subject = null;

    public function getSubject(): ?Order
    {
        return $this->subject;
    }

    public function setSubject(?Order $subject): static
    {
        $this->title = 'Test Order';
        $this->amount = $subject?->getTotal();
        $this->subject = $subject;

        return $this;
    }
}
