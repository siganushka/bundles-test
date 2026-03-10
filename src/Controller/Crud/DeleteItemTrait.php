<?php

declare(strict_types=1);

namespace App\Controller\Crud;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

trait DeleteItemTrait
{
    use ApiOperationsTrait;

    #[Route('/{_id<\d+>}', methods: 'DELETE')]
    public function deleteItem(string $_id): Response
    {
        $entity = $this->findEntity($_id);

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        // 204 No Content
        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
