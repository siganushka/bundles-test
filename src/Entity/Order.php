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

#[ORM\Entity(repositoryClass: OrderRepository::class)]
class Order extends BaseOrder implements DeletableInterface
{
    use DeletableTrait;

    /**
     * @var Collection<int, OrderTransaction>
     */
    #[ORM\OneToMany(targetEntity: OrderTransaction::class, mappedBy: 'order')]
    private Collection $transactions;

    /**
     * @var Collection<int, OrderTransactionAggregate>
     */
    #[ORM\ManyToMany(targetEntity: OrderTransactionAggregate::class, mappedBy: 'orders')]
    private Collection $aggregateTransactions;

    public function __construct()
    {
        parent::__construct();

        $this->transactions = new ArrayCollection();
        $this->aggregateTransactions = new ArrayCollection();
    }

    /**
     * @return Collection<int, OrderTransaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(OrderTransaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setOrder($this);
        }

        return $this;
    }

    public function removeTransaction(OrderTransaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getOrder() === $this) {
                $transaction->setOrder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderTransactionAggregate>
     */
    public function getAggregateTransactions(): Collection
    {
        return $this->aggregateTransactions;
    }

    public function addAggregateTransaction(OrderTransactionAggregate $aggregateTransaction): static
    {
        if (!$this->aggregateTransactions->contains($aggregateTransaction)) {
            $this->aggregateTransactions->add($aggregateTransaction);
            $aggregateTransaction->addOrder($this);
        }

        return $this;
    }

    public function removeAggregateTransaction(OrderTransactionAggregate $aggregateTransaction): static
    {
        if ($this->aggregateTransactions->removeElement($aggregateTransaction)) {
            $aggregateTransaction->removeOrder($this);
        }

        return $this;
    }
}
