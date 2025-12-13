<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class TestEmbeddable
{
    #[ORM\Column]
    private ?string $foo = null;

    public function getFoo(): ?string
    {
        return $this->foo;
    }

    public function setFoo(string $foo): static
    {
        $this->foo = $foo;

        return $this;
    }
}
