<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RegionRepository;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\RegionBundle\Entity\Region as BaseRegion;

#[ORM\Entity(repositoryClass: RegionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Region extends BaseRegion
{
    #[ORM\Column(length: 255, nullable: false)]
    private ?string $fullname = null;

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): static
    {
        $this->fullname = $fullname;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->fullname = implode('/', array_map(fn (Region $region) => $region->getName(), $this->getAncestors(true)));
    }
}
