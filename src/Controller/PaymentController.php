<?php

declare(strict_types=1);

namespace App\Controller;

use Siganushka\GenericBundle\Controller\Crud\Web\IndexTrait;
use Siganushka\PaymentBundle\Entity\Payment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/payments')]
class PaymentController extends AbstractController
{
    use IndexTrait;

    public function __construct()
    {
        $this->configureCrud(
            entityName: Payment::class,
        );
    }
}
