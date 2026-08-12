<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Siganushka\PaymentBundle\Entity\AbstractPaymentRefund;

#[ORM\Entity]
class PaymentRefund extends AbstractPaymentRefund
{
}
