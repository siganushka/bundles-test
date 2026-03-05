<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

trait GetItemTrait
{
    use HttpOperationTrait;

    #[Route('/{_id<\d+>}', methods: 'GET')]
    public function getItem(EntityManagerInterface $em, SerializerInterface $serializer, string $_id): Response
    {
        $entity = $this->findEntity($em, $_id);

        $data = $serializer->serialize($entity, 'json', [
            AbstractNormalizer::GROUPS => \sprintf('%s:item', $this->entityAlias),
        ]);

        return new JsonResponse($data, json: true);
    }
}
