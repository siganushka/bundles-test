<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\TransactionBundle\Entity\Transaction;

#[ORM\Entity]
class OrderTransaction extends Transaction
{
}
