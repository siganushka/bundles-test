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
    private readonly User $user;

    #[ORM\ManyToOne]
    private readonly Topup $topup;

    public function __construct(string $gateway, User $user, Topup $topup)
    {
        $this->amount = $topup->getAmount();
        $this->currency = 'CNY';
        $this->topup = $topup;
        $this->user = $user;

        parent::__construct($gateway);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTopup(): Topup
    {
        return $this->topup;
    }

    public function getTitleParameters(): array
    {
        return ['%id%' => $this->topup->getId()];
    }

    public function supportsRefund(): bool
    {
        return false;
    }
}
