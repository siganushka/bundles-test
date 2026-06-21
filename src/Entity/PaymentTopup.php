<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\PaymentBundle\Entity\Payment;

#[ORM\Entity]
class PaymentTopup extends Payment
{
    use PaymentContext;

    #[ORM\ManyToOne(inversedBy: 'topups')]
    private ?User $user = null;

    #[ORM\ManyToOne]
    private ?Topup $topup = null;

    public function __construct(User $user, Topup $topup)
    {
        $this->amount = $topup->getAmount();
        $this->currency = 'CNY';
        $this->topup = $topup;
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getTopup(): ?Topup
    {
        return $this->topup;
    }

    public function getTitleParameters(): array
    {
        return ['%id%' => $this->topup?->getId()];
    }
}
