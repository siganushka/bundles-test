<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

trait DeleteItemTrait
{
    use HttpOperationTrait;

    #[Route('/{identifier}', methods: 'DELETE')]
    public function deleteItem(EntityManagerInterface $entityManager, string $identifier): Response
    {
        $entity = $this->findEntity($entityManager, $identifier);

        $entityManager->remove($entity);
        $entityManager->flush();

        // 204 No Content
        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
