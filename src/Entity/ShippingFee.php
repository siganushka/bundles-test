<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\OrderBundle\Entity\OrderAdjustment;
use Siganushka\OrderBundle\Repository\OrderAdjustmentRepository;

#[ORM\Entity(repositoryClass: OrderAdjustmentRepository::class)]
class ShippingFee extends OrderAdjustment
{
}
