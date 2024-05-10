<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserAddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;
use Siganushka\Contracts\Doctrine\TimestampableInterface;
use Siganushka\Contracts\Doctrine\TimestampableTrait;
use Siganushka\RegionBundle\Entity\Region;

/**
 * @ORM\Entity(repositoryClass=UserAddressRepository::class)
 */
class UserAddress implements ResourceInterface, TimestampableInterface
{
    use ResourceTrait;
    use TimestampableTrait;

    /**
     * @ORM\OneToOne(targetEntity=Trade::class, inversedBy="shipping", cascade={"persist", "remove"})
     */
    private ?Trade $trade = null;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class)
     */
    private ?Region $province = null;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class)
     */
    private ?Region $city = null;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class)
     */
    private ?Region $district = null;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class)
     */
    private ?Region $street = null;

    public function getTrade(): ?Trade
    {
        return $this->trade;
    }

    public function setTrade(?Trade $trade): self
    {
        // unset the owning side of the relation if necessary
        if (null === $trade && null !== $this->trade) {
            $this->trade->setShipping(null);
        }

        // set the owning side of the relation if necessary
        if (null !== $trade && $trade->getShipping() !== $this) {
            $trade->setShipping($this);
        }

        $this->trade = $trade;

        return $this;
    }

    public function getProvince(): ?Region
    {
        return $this->province;
    }

    public function setProvince(?Region $province): self
    {
        $this->province = $province;

        return $this;
    }

    public function getCity(): ?Region
    {
        return $this->city;
    }

    public function setCity(?Region $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getDistrict(): ?Region
    {
        return $this->district;
    }

    public function setDistrict(?Region $district): self
    {
        $this->district = $district;

        return $this;
    }

    public function getStreet(): ?Region
    {
        return $this->street;
    }

    public function setStreet(?Region $street): self
    {
        $this->street = $street;

        return $this;
    }
}
