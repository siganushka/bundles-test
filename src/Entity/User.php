<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\UserBundle\Entity\User as BaseUser;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User extends BaseUser
{
    #[ORM\Column]
    private int $balance = 0;

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): static
    {
        $this->balance = $balance;

        return $this;
    }
}
