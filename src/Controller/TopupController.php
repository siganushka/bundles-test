<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Topup;
use Siganushka\GenericBundle\Controller\Crud\GetCollectionTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/topups')]
class TopupController extends AbstractController
{
    use GetCollectionTrait;

    public function __construct()
    {
        $this->configureCrud(
            entityName: Topup::class,
        );
    }
}
