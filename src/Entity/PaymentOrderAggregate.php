<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;

#[ORM\Entity]
class PaymentOrderAggregate extends Payment
{
    /**
     * @var Collection<array-key, Order>
     */
    #[ORM\ManyToMany(targetEntity: Order::class, inversedBy: 'aggregatePayments')]
    #[ORM\JoinTable('payment_order')]
    #[ORM\JoinColumn('payment_id')]
    private Collection $orders;

    /**
     * @param array<array-key, Order> $orders
     */
    public function __construct(string $gateway, array $orders, ?\DateTimeImmutable $expiredAt = null)
    {
        $this->amount = array_reduce($orders, static fn (int $carry, Order $item) => $carry + $item->getTotal(), 0);
        $this->currency = 'CNY';
        $this->orders = new ArrayCollection($orders);
        $this->expiredAt = $expiredAt;

        parent::__construct($gateway);
    }

    /**
     * @return Collection<array-key, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function getTitle(): string|TranslatableInterface
    {
        $numbers = $this->orders->map(static fn (Order $item) => $item->getNumber());

        return new TranslatableMessage(\sprintf('payment.type.%s', $this->getType()), ['%numbers%' => implode(', ', $numbers->toArray())]);
    }
}
