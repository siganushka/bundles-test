<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

trait DeleteItemTrait
{
    use HttpOperationTrait;

    #[Route('/{identifier}', methods: 'DELETE')]
    public function deleteItem(EntityManagerInterface $entityManager, int $identifier): Response
    {
        $criteria = [$this->getIdentifierName() => $identifier];

        $entity = $entityManager->getRepository($this->getEntityFqcn())->findOneBy($criteria)
            ?? throw new NotFoundHttpException('Not Found');

        $entityManager->remove($entity);
        $entityManager->flush();

        // 204 No Content
        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
