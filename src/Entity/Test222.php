<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Test222 extends TestInheritanceJoined
{
    #[ORM\Column]
    private ?string $b = null;

    public function getB(): ?string
    {
        return $this->b;
    }

    public function setB(string $b): static
    {
        $this->b = $b;

        return $this;
    }
}
