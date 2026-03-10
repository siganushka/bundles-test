<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Crud\DeleteItemTrait;
use App\Controller\Crud\GetCollectionTrait;
use App\Controller\Crud\GetItemTrait;
use App\Controller\Crud\PostCollectionTrait;
use App\Controller\Crud\PutItemTrait;
use App\Entity\Product;
use Siganushka\ProductBundle\Form\ProductType;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rest/products')]
class TestController
{
    use GetCollectionTrait;
    use PostCollectionTrait;
    use GetItemTrait;
    use PutItemTrait;
    use DeleteItemTrait;

    public function __construct()
    {
        $this->configureCrud(
            entityName: Product::class,
            entityForm: ProductType::class,
        );
    }
}
