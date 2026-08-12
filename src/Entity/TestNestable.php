<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\Contracts\Doctrine\ResourceTrait;
use Siganushka\GenericBundle\Entity\AbstractNestable;

#[ORM\Entity]
class TestNestable extends AbstractNestable implements ResourceInterface
{
    use ResourceTrait;
}
