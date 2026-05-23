<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;
use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\TimestampableTrait;

#[ORM\Entity]
class Topup implements ResourceInterface, TimestampableInterface
{
    use ResourceTrait;
    use TimestampableTrait;

    #[ORM\Column]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $amount = null;

    /**
     * @var Collection<int, TopupTransaction>
     */
    #[ORM\OneToMany(targetEntity: TopupTransaction::class, mappedBy: 'topup')]
    private Collection $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
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
     * @return Collection<int, TopupTransaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(TopupTransaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setTopup($this);
        }

        return $this;
    }

    public function removeTransaction(TopupTransaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getTopup() === $this) {
                $transaction->setTopup(null);
            }
        }

        return $this;
    }
}
