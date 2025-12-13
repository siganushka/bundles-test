<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Test333 extends TestInheritanceSingleTable
{
    #[ORM\Column]
    private ?string $c = null;

    public function getC(): ?string
    {
        return $this->c;
    }

    public function setC(string $c): static
    {
        $this->c = $c;

        return $this;
    }
}
