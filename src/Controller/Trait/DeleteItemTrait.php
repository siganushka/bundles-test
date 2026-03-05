<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

trait DeleteItemTrait
{
    use HttpOperationTrait;

    #[Route('/{_id<\d+>}', methods: 'DELETE')]
    public function deleteItem(EntityManagerInterface $em, string $_id): Response
    {
        $entity = $this->findEntity($em, $_id);

        $em->remove($entity);
        $em->flush();

        // 204 No Content
        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
