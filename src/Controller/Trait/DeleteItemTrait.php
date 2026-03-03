<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

trait DeleteItemTrait
{
    use HttpOperationTrait;

    #[Route('/{_id}', methods: 'DELETE')]
    public function deleteItem(EntityManagerInterface $entityManager, string $_id): Response
    {
        $entity = $this->findEntity($entityManager, $_id);

        $entityManager->remove($entity);
        $entityManager->flush();

        // 204 No Content
        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
