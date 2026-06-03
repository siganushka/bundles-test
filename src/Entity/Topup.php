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
     * @var Collection<int, TransactionTopup>
     */
    #[ORM\OneToMany(targetEntity: TransactionTopup::class, mappedBy: 'topup')]
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
     * @return Collection<int, TransactionTopup>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(TransactionTopup $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setTopup($this);
        }

        return $this;
    }

    public function removeTransaction(TransactionTopup $transaction): static
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
