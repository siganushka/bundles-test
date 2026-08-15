<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\OrderBundle\Repository\OrderAdjustmentRepository;

#[ORM\Entity]
class OrderAdjustmentShipping extends OrderAdjustment
{
}
