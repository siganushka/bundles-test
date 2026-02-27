<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Trait\DeleteItemTrait;
use App\Controller\Trait\GetCollectionTrait;
use App\Controller\Trait\GetItemTrait;
use App\Controller\Trait\PostCollectionTrait;
use App\Controller\Trait\PutItemTrait;
use App\Entity\Product;
use Siganushka\ProductBundle\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/test-curd-routes')]
class TestController extends AbstractController
{
    use GetCollectionTrait;
    use GetItemTrait;
    use PostCollectionTrait;
    use PutItemTrait;
    use DeleteItemTrait;

    /**
     * @return class-string<object>
     */
    protected function getEntityFqcn(): string
    {
        return Product::class;
    }

    /**
     * @return class-string<FormTypeInterface>
     */
    protected function getFormType(): string
    {
        return ProductType::class;
    }
}
