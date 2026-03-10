<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Crud\EditTrait;
use App\Entity\ProductOption;
use Siganushka\ProductBundle\Form\ProductOptionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/product-options')]
class ProductOptionController extends AbstractController
{
    use EditTrait;

    public function __construct()
    {
        $this->configureCrud(
            entityName: ProductOption::class,
            entityForm: ProductOptionType::class,
        );
    }

    protected function getControllerAlias(): string
    {
        return 'product';
    }
}
