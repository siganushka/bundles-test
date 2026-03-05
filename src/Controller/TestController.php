<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Trait\DeleteItemTrait;
use App\Controller\Trait\GetCollectionTrait;
use App\Controller\Trait\GetItemTrait;
use App\Controller\Trait\PostCollectionTrait;
use App\Controller\Trait\PutItemTrait;
use Symfony\Component\Routing\Attribute\Route;

#[Route('tests')]
class TestController
{
    use GetCollectionTrait;
    use PostCollectionTrait;
    use GetItemTrait;
    use PutItemTrait;
    use DeleteItemTrait;
}
