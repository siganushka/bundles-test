<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

trait GetCollectionTrait
{
    use HttpOperationTrait;

    #[Route(methods: 'GET')]
    public function getCollection(EntityManagerInterface $entityManager, SerializerInterface $serializer, PaginatorInterface $paginator): Response
    {
        $repository = $entityManager->getRepository($this->getEntityFqcn());

        $queryBuilder = $repository instanceof GenericEntityRepository
            ? $repository->createQueryBuilderWithOrderBy('entity')
            : $repository->createQueryBuilder('entity');

        $pagination = $paginator->paginate($queryBuilder);
        $data = $serializer->serialize($pagination, 'json', [
            AbstractNormalizer::GROUPS => \sprintf('%s:collection', $this->getEntityAlias()),
        ]);

        return new JsonResponse($data, json: true);
    }
}
