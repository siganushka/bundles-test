<?php

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\Crud;
use App\Entity\Order;

#[Crud(entityFqcn: Order::class, entityIdentifier: 'number')]
class DemoController
{
}
