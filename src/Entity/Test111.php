<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Test111 extends TestInheritanceJoined
{
    #[ORM\Column]
    private ?string $a = null;

    public function getA(): ?string
    {
        return $this->a;
    }

    public function setA(string $a): static
    {
        $this->a = $a;

        return $this;
    }
}
