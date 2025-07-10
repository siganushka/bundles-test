<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\DeletableInterface;
use Siganushka\Contracts\Doctrine\DeletableTrait;
use Siganushka\OrderBundle\Entity\Order as BaseOrder;

#[ORM\Entity]
class Order extends BaseOrder implements DeletableInterface
{
    use DeletableTrait;
}
