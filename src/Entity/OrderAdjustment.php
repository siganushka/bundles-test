<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\OrderBundle\Entity\OrderAdjustment as BaseOrderAdjustment;

#[ORM\Entity]
class OrderAdjustment extends BaseOrderAdjustment
{
}
