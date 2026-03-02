<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

trait PutItemTrait
{
    use HttpOperationTrait;

    #[Route('/{identifier}', methods: ['PUT', 'PATCH'])]
    public function putItem(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        FormFactoryInterface $factory,
        mixed $identifier,
    ): Response {
        $criteria = [$this->getIdentifierName() => $identifier];

        $entity = $entityManager->getRepository($this->getEntityFqcn())->findOneBy($criteria)
            ?? throw new NotFoundHttpException('Not Found');

        $form = $factory->create($this->getFormType(), $entity);
        $form->submit($request->getPayload()->all(), !$request->isMethod('PATCH'));

        if (!$form->isValid()) {
            return new JsonResponse($serializer->serialize($form, 'json'), Response::HTTP_UNPROCESSABLE_ENTITY, json: true);
        }

        $entityManager->persist($entity);
        $entityManager->flush();

        $data = $serializer->serialize($entity, 'json', [
            AbstractNormalizer::GROUPS => \sprintf('%s:item', $this->getEntityAlias()),
        ]);

        return new JsonResponse($data, json: true);
    }
}
