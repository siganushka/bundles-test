<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\PaymentBundle\Entity\Payment;

#[ORM\Entity]
class PaymentOrder extends Payment
{
    use PaymentContext;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    private ?Order $order = null;

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): static
    {
        $this->title = \sprintf('Test Order (%d items)', $order?->getItems()->count() ?? 0);
        $this->amount = $order?->getTotal();
        $this->order = $order;

        return $this;
    }
}
