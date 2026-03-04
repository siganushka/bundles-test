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
     * @var class-string<object>
     */
    private string $entityFqcn;

    /**
     * @var class-string<FormTypeInterface>
     */
    private string $entityForm;

    private string $entityIdentifier;
    private string $entityAlias;
    private string $controllerAlias;
    private string $templateAlias;

    /**
     * @param class-string<object>                 $entityFqcn
     * @param class-string<FormTypeInterface>|null $entityForm
     */
    protected function configureCrud(
        string $entityFqcn,
        ?string $entityForm = null,
        ?string $entityIdentifier = null,
        ?string $entityAlias = null,
        ?string $controllerAlias = null,
        ?string $templateAlias = null,
    ): void {
        $this->entityFqcn = $entityFqcn;
        $this->entityForm = $entityForm ?? FormType::class;
        $this->entityIdentifier = $entityIdentifier ?? 'id';
        $this->entityAlias = $entityAlias ?? $this->generateAlias($entityFqcn);
        $this->controllerAlias = $controllerAlias ?? str_replace('_', '', rtrim($this->generateAlias($this), '_controller'));
        $this->templateAlias = $templateAlias ?? rtrim($this->generateAlias($this), '_controller');
    }

    protected function createQueryBuilderForRequest(Request $request, EntityManagerInterface $em): QueryBuilder
    {
        $repository = $em->getRepository($this->entityFqcn);

        return $repository instanceof GenericEntityRepository
            ? $repository->createQueryBuilderWithOrderBy('entity')
            : $repository->createQueryBuilder('entity');
    }

    protected function createEntity(EntityManagerInterface $em): object
    {
        $repository = $em->getRepository($this->entityFqcn);

        return $repository instanceof GenericEntityRepository
            ? $repository->createNew()
            : (new \ReflectionClass($repository->getClassName()))->newInstance();
    }

    protected function findEntity(EntityManagerInterface $em, string $_id): object
    {
        $entity = $em->getRepository($this->entityFqcn)->findOneBy([$this->entityIdentifier => $_id])
            ?? throw new NotFoundHttpException('Not Found');

        return $entity;
    }

    /**
     * @param object|class-string $objectOrClass
     */
    private function generateAlias(object|string $objectOrClass): string
    {
        $ref = new \ReflectionClass($objectOrClass);
        /** @var string */
        $class = preg_replace('/([a-z])([A-Z])/', '$1_$2', $ref->getShortName());

        return strtolower($class);
    }
}
