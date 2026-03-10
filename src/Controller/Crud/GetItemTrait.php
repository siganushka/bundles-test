<?php

declare(strict_types=1);

namespace App\Controller\Crud;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

trait GetItemTrait
{
    use ApiOperationsTrait;

    #[Route('/{_id<\d+>}', methods: 'GET')]
    public function getItem(SerializerInterface $serializer, string $_id): Response
    {
        $entity = $this->findEntity($_id);

        $data = $serializer->serialize($entity, 'json', $this->getSerializationItemContext());

        return new JsonResponse($data, json: true);
    }
}
