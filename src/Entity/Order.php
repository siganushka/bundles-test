<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Siganushka\Contracts\Doctrine\DeletableInterface;
use Siganushka\Contracts\Doctrine\DeletableTrait;
use Siganushka\OrderBundle\Entity\Order as BaseOrder;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
class Order extends BaseOrder implements DeletableInterface
{
    use DeletableTrait;
}
