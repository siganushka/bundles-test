<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\PaymentBundle\Entity\AbstractPayment;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;

#[ORM\Entity]
class PaymentOrder extends Payment
{
    #[ORM\ManyToOne(inversedBy: 'payments')]
    private readonly Order $order;

    public function __construct(string $gateway, Order $order, ?\DateTimeImmutable $expiredAt = null)
    {
        $this->amount = $order->getTotal();
        $this->currency = 'CNY';
        $this->order = $order;
        $this->expiredAt = $expiredAt;

        parent::__construct($gateway);
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function getTitle(): string|TranslatableInterface
    {
        return new TranslatableMessage(\sprintf('payment.type.%s', $this->getType()), ['%number%' => $this->order->getNumber()]);
    }
}
