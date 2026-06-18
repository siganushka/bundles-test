<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\PaymentBundle\Entity\Payment;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class PaymentOrderAggregate extends Payment
{
    use PaymentContext;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\ManyToMany(targetEntity: Order::class)]
    #[ORM\JoinTable('payment_order')]
    #[ORM\JoinColumn('payment_id')]
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
            $this->orders->add($order);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        $this->orders->removeElement($order);

        return $this;
    }

    public function getTitleParameters(): array
    {
        $numbers = $this->orders->map(static fn (Order $item) => $item->getNumber());

        return ['%numbers%' => implode(', ', $numbers->toArray())];
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function computed(): void
    {
        $this->getAmount();
    }
}
