<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\OrderBundle\Entity\AbstractOrderAdjustment;

#[ORM\Entity]
#[ORM\InheritanceType(value: 'SINGLE_TABLE')]
class OrderAdjustment extends AbstractOrderAdjustment
{
}
