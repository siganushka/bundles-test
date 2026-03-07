<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

trait GetCollectionTrait
{
    use OperationsTrait;

    #[Route(methods: 'GET')]
    public function getCollection(SerializerInterface $serializer, PaginatorInterface $paginator): Response
    {
        $qb = $this->createEntityQueryBuilder('entity');
        $pagination = $paginator->paginate($qb);

        $data = $serializer->serialize($pagination, 'json', $this->serializationCollectionContext);

        return new JsonResponse($data, json: true);
    }
}
