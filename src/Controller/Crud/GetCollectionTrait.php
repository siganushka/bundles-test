<?php

declare(strict_types=1);

namespace App\Controller\Crud;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

trait GetCollectionTrait
{
    use ApiOperationsTrait;

    #[Route(methods: 'GET')]
    public function getCollection(SerializerInterface $serializer, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->createEntityQueryBuilder('entity');
        $query = $queryBuilder->getQuery();

        $data = $this->pagination
            ? $paginator->paginate($query)
            : $query->getResult();

        $json = $serializer->serialize($data, 'json', $this->getSerializationCollectionContext());

        return new JsonResponse($json, json: true);
    }
}
