<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Test555 extends TestMappedSuperclass
{
    #[ORM\Column]
    private ?string $e = null;

    public function getE(): ?string
    {
        return $this->e;
    }

    public function setE(string $e): static
    {
        $this->e = $e;

        return $this;
    }
}
