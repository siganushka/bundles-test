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
    private readonly Order $order;

    public function __construct(Order $order, ?\DateTimeImmutable $expiredAt = null)
    {
        $this->amount = $order->getTotal();
        $this->currency = 'CNY';
        $this->order = $order;
        $this->expiredAt = $expiredAt;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function getTitleParameters(): array
    {
        return ['%number%' => $this->order->getNumber()];
    }
}
