<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\PaymentBundle\Entity\Payment;

#[ORM\Entity]
class PaymentOrderAggregate extends Payment
{
    use PaymentContext;

    /**
     * @var Collection<array-key, Order>
     */
    #[ORM\ManyToMany(targetEntity: Order::class, inversedBy: 'aggregatePayments')]
    #[ORM\JoinTable('payment_order')]
    #[ORM\JoinColumn('payment_id')]
    private Collection $orders;

    public function __construct(Order ...$orders)
    {
        $this->amount = array_reduce($orders, static fn (int $carry, Order $item) => $carry + $item->getTotal(), 0);
        $this->currency = 'CNY';
        $this->orders = new ArrayCollection($orders);
    }

    /**
     * @return Collection<array-key, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function getTitleParameters(): array
    {
        $numbers = $this->orders->map(static fn (Order $item) => $item->getNumber());

        return ['%numbers%' => implode(', ', $numbers->toArray())];
    }
}
