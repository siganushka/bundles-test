<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\UserBundle\Entity\User as BaseUser;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User extends BaseUser
{
    #[ORM\Column]
    private int $balance = 0;

    /**
     * @var Collection<int, PaymentTopup>
     */
    #[ORM\OneToMany(targetEntity: PaymentTopup::class, mappedBy: 'user')]
    private Collection $topups;

    public function __construct()
    {
        $this->topups = new ArrayCollection();

        parent::__construct();
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return Collection<int, PaymentTopup>
     */
    public function getTopups(): Collection
    {
        return $this->topups;
    }
}
