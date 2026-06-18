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
        $this->amount = $order?->getTotal();
        $this->order = $order;

        return $this;
    }

    public function getTitleParameters(): array
    {
        return ['%number%' => $this->order?->getNumber()];
    }
}
