<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\GenericBundle\Entity\Embeddable\DateRange;

#[ORM\Entity]
class Test111 extends TestInheritanceJoined
{
    #[ORM\Column]
    private ?string $a = null;

    #[ORM\Embedded(columnPrefix: false)]
    private ?DateRange $range = null;

    public function getA(): ?string
    {
        return $this->a;
    }

    public function setA(string $a): static
    {
        $this->a = $a;

        return $this;
    }

    public function getRange(): ?DateRange
    {
        return $this->range;
    }

    public function setRange(DateRange $range): ?static
    {
        $this->range = $range;

        return $this;
    }
}
