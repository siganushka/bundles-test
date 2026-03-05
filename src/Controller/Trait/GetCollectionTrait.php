<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

trait GetCollectionTrait
{
    use HttpOperationTrait;

    #[Route(methods: 'GET')]
    public function getCollection(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        PaginatorInterface $paginator,
    ): Response {
        $qb = $this->createQueryBuilderForRequest($request, $em);
        $pagination = $paginator->paginate($qb);

        $data = $serializer->serialize($pagination, 'json', [
            AbstractNormalizer::GROUPS => \sprintf('%s:collection', $this->entityAlias),
        ]);

        return new JsonResponse($data, json: true);
    }
}
