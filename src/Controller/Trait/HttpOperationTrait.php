<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait HttpOperationTrait
{
    /**
     * @return class-string<object>
     */
    abstract protected function getEntityFqcn(): string;

    /**
     * @return class-string<FormTypeInterface>
     */
    protected function getEntityForm(): string
    {
        return FormType::class;
    }

    protected function getEntityAlias(): string
    {
        $ref = new \ReflectionClass($this->getEntityFqcn());
        /** @var string */
        $class = preg_replace('/([a-z])([A-Z])/', '$1_$2', $ref->getShortName());

        return strtolower($class);
    }

    protected function getEntityIdentifier(): string
    {
        return 'id';
    }

    protected function validateEntityIdentifier(string $identifier): bool
    {
        return (bool) preg_match('/^\d+$/', $identifier);
    }

    protected function createQueryBuilderForRequest(Request $request, EntityManagerInterface $em): QueryBuilder
    {
        $repository = $em->getRepository($this->getEntityFqcn());

        return $repository instanceof GenericEntityRepository
            ? $repository->createQueryBuilderWithOrderBy('entity')
            : $repository->createQueryBuilder('entity');
    }

    protected function createEntity(EntityManagerInterface $entityManager): object
    {
        $repository = $entityManager->getRepository($this->getEntityFqcn());
        if ($repository instanceof GenericEntityRepository) {
            return $repository->createNew();
        }

        return (new \ReflectionClass($repository->getClassName()))->newInstance();
    }

    protected function findEntity(EntityManagerInterface $em, string $identifier): object
    {
        if (!$this->validateEntityIdentifier($identifier)) {
            throw new NotFoundHttpException('Not Found');
        }

        $entity = $em->getRepository($this->getEntityFqcn())->findOneBy([$this->getEntityIdentifier() => $identifier])
            ?? throw new NotFoundHttpException('Not Found');

        return $entity;
    }
}
