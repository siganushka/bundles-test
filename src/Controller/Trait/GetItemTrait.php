<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

trait GetItemTrait
{
    use HttpOperationTrait;

    #[Route('/{identifier}', methods: 'GET')]
    public function getItem(EntityManagerInterface $entityManager, SerializerInterface $serializer, string $identifier): Response
    {
        $criteria = [$this->getIdentifierName() => $identifier];

        $entity = $entityManager->getRepository($this->getEntityFqcn())->findOneBy($criteria)
            ?? throw new NotFoundHttpException('Not Found');

        $data = $serializer->serialize($entity, 'json', [
            AbstractNormalizer::GROUPS => \sprintf('%s:item', $this->getEntityAlias()),
        ]);

        return new JsonResponse($data, json: true);
    }
}
