<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Override;
use Siganushka\PaymentBundle\Entity\Payment;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatableInterface;

#[ORM\Entity]
class PaymentTopup extends Payment
{
    use PaymentContext;

    #[ORM\ManyToOne(inversedBy: 'topups')]
    private ?User $user = null;

    #[ORM\ManyToOne]
    private ?Topup $topup = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTopup(): ?Topup
    {
        return $this->topup;
    }

    public function setTopup(?Topup $topup): static
    {
        $this->amount = $topup?->getAmount();
        $this->topup = $topup;

        return $this;
    }

    public function getTitle(): string|TranslatableInterface
    {
        return $this->topup->getTitle();
    }
}
