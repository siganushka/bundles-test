<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use Siganushka\GenericBundle\Controller\Crud\DeleteItemTrait;
use Siganushka\GenericBundle\Controller\Crud\GetCollectionTrait;
use Siganushka\GenericBundle\Controller\Crud\GetItemTrait;
use Siganushka\GenericBundle\Controller\Crud\PostCollectionTrait;
use Siganushka\GenericBundle\Controller\Crud\PutItemTrait;
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
