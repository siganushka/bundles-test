<?php

declare(strict_types=1);

namespace App\Controller;

use Siganushka\GenericBundle\Controller\Crud\Web\IndexTrait;
use Siganushka\TransactionBundle\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/transactions')]
class TransactionController extends AbstractController
{
    use IndexTrait;

    public function __construct()
    {
        $this->configureCrud(
            entityName: Transaction::class,
        );
    }
}
