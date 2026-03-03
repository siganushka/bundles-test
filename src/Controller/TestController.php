<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Trait\DeleteItemTrait;
use App\Controller\Trait\GetCollectionTrait;
use App\Controller\Trait\GetItemTrait;
use App\Controller\Trait\PostCollectionTrait;
use App\Controller\Trait\PutItemTrait;
use App\Entity\Product;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/test-crud-routes', requirements: ['_id' => Requirement::DIGITS])]
class TestController
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
}
