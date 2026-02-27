<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

trait PostCollectionTrait
{
    use HttpOperationTrait;

    #[Route(methods: 'POST')]
    public function postCollection(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        FormFactoryInterface $factory,
    ): Response {
        $entity = new ($this->getEntityFqcn());

        $form = $factory->create($this->getFormType(), $entity);
        $form->submit($request->getPayload()->all());

        if (!$form->isValid()) {
            return new JsonResponse($serializer->serialize($form, 'json'), Response::HTTP_UNPROCESSABLE_ENTITY, json: true);
        }

        $entityManager->persist($entity);
        $entityManager->flush();

        $data = $serializer->serialize($entity, 'json', [
            AbstractNormalizer::GROUPS => \sprintf('%s:item', $this->getEntityAlias()),
        ]);

        return new JsonResponse($data, Response::HTTP_CREATED, json: true);
    }
}
