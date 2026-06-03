<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\TransactionBundle\Entity\Transaction;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class TransactionOrderAggregate extends Transaction
{
    /**
     * @var Collection<int, Order>
     */
    #[ORM\ManyToMany(targetEntity: Order::class, inversedBy: 'aggregateTransactions')]
    #[ORM\JoinTable('transaction_order')]
    #[ORM\JoinColumn('transaction_id')]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getAmount(): ?int
    {
        return $this->amount ??= $this->orders->reduce(static fn (int $carry, Order $item) => $carry + $item->getTotal(), 0);
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->amount = null;
            $this->orders->add($order);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        $this->amount = null;
        $this->orders->removeElement($order);

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function computed(): void
    {
        $this->getAmount();
    }
}
