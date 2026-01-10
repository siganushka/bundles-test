<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TestMappedSuperclassRepository;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;

#[ORM\MappedSuperclass(repositoryClass: TestMappedSuperclassRepository::class)]
abstract class TestMappedSuperclass implements ResourceInterface
{
    use ResourceTrait;

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
