<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Trait\Web\DeleteTrait;
use App\Controller\Trait\Web\EditTrait;
use App\Controller\Trait\Web\IndexTrait;
use App\Controller\Trait\Web\NewTrait;
use App\Controller\Trait\Web\ShowTrait;
use App\Entity\Product;
use Siganushka\ProductBundle\Form\ProductType;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/test-crud-routes', requirements: ['_id' => Requirement::DIGITS])]
class TestWebController
{
    use IndexTrait;
    use NewTrait;
    use ShowTrait;
    use EditTrait;
    use DeleteTrait;

    public function __construct()
    {
        $this->configureCrud(
            entityFqcn: Product::class,
            entityForm: ProductType::class,
        );
    }
}
