<?php

declare(strict_types=1);

namespace App\Controller;

use Siganushka\GenericBundle\Controller\Crud\DeleteItemTrait;
use Siganushka\GenericBundle\Controller\Crud\GetCollectionTrait;
use Siganushka\GenericBundle\Controller\Crud\GetItemTrait;
use Siganushka\GenericBundle\Controller\Crud\PostCollectionTrait;
use Siganushka\GenericBundle\Controller\Crud\PutItemTrait;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rest/tests')]
class TestController
{
    use GetCollectionTrait;
    use PostCollectionTrait;
    use GetItemTrait;
    use PutItemTrait;
    use DeleteItemTrait;
}
