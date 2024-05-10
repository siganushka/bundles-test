<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TradeRepository;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\OrderBundle\Entity\Order;

/**
 * @ORM\Entity(repositoryClass=TradeRepository::class)
 */
class Trade extends Order
{
    /**
     * @ORM\OneToOne(targetEntity=UserAddress::class, mappedBy="trade", cascade={"persist", "remove"})
     */
    private ?UserAddress $shipping = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $state = null;

    public function getShipping(): ?UserAddress
    {
        return $this->shipping;
    }

    public function setShipping(?UserAddress $shipping): self
    {
        $this->shipping = $shipping;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }
}
