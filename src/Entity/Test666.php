<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Test666 extends TestMappedSuperclass
{
    #[ORM\Embedded(TestEmbeddable::class)]
    private ?TestEmbeddable $f = null;

    public function getF(): ?TestEmbeddable
    {
        return $this->f;
    }

    public function setF(TestEmbeddable $f): static
    {
        $this->f = $f;

        return $this;
    }
}
