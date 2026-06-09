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
     * @var Collection<int, PaymentOrder>
     */
    #[ORM\OneToMany(targetEntity: PaymentOrder::class, mappedBy: 'subject')]
    private Collection $payments;

    public function __construct()
    {
        parent::__construct();

        $this->payments = new ArrayCollection();
    }

    /**
     * @return Collection<int, PaymentOrder>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(PaymentOrder $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setSubject($this);
        }

        return $this;
    }

    public function removePayment(PaymentOrder $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getSubject() === $this) {
                $payment->setSubject(null);
            }
        }

        return $this;
    }
}
