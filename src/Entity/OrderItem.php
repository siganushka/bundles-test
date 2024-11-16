<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\OrderBundle\Entity\OrderItem as BaseOrderItem;

#[ORM\Entity]
class OrderItem extends BaseOrderItem
{
}