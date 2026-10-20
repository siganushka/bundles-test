<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\InheritanceType('SINGLE_TABLE')]
class TestInheritanceSingleTable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $foo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

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
