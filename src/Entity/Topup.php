<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TopupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;
use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\TimestampableTrait;

#[ORM\Entity(repositoryClass: TopupRepository::class)]
class Topup implements ResourceInterface, TimestampableInterface
{
    use ResourceTrait;
    use TimestampableTrait;

    #[ORM\Column]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $amount = null;

    /**
     * @var Collection<int, PaymentTopup>
     */
    #[ORM\OneToMany(targetEntity: PaymentTopup::class, mappedBy: 'topup')]
    private Collection $payments;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return Collection<int, PaymentTopup>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(PaymentTopup $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setTopup($this);
        }

        return $this;
    }

    public function removePayment(PaymentTopup $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getTopup() === $this) {
                $payment->setTopup(null);
            }
        }

        return $this;
    }
}
