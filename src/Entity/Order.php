<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\DeletableInterface;
use Siganushka\Contracts\Doctrine\DeletableTrait;
use Siganushka\OrderBundle\Entity\Order as BaseOrder;
use Siganushka\PaymentBundle\Entity\Payment;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
class Order extends BaseOrder implements DeletableInterface
{
    use DeletableTrait;

    /**
     * @var Collection<int, PaymentOrder>
     */
    #[ORM\OneToMany(targetEntity: PaymentOrder::class, mappedBy: 'order')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    private Collection $payments;

    /**
     * @var Collection<int, PaymentOrderAggregate>
     */
    #[ORM\ManyToMany(targetEntity: PaymentOrderAggregate::class, mappedBy: 'orders')]
    private Collection $aggregatePayments;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
        $this->aggregatePayments = new ArrayCollection();

        parent::__construct();
    }

    /**
     * @return Collection<int, PaymentOrder>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    /**
     * @return Collection<int, PaymentOrderAggregate>
     */
    public function getAggregatePayments(): Collection
    {
        return $this->aggregatePayments;
    }

    public function getCurrentPayment(): ?Payment
    {
        return $this->payments->last() ?: ($this->aggregatePayments->last() ?: null);
    }
}
