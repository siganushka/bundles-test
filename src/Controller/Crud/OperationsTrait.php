<?php

declare(strict_types=1);

namespace App\Controller\Crud;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Service\Attribute\Required;

trait OperationsTrait
{
    #[Required]
    public EntityManagerInterface $entityManager;

    #[Required]
    public FormFactoryInterface $formFactory;

    /**
     * @var class-string<object>
     */
    protected string $entityName;
    protected string $entityIdentifier;

    /**
     * @var class-string<FormTypeInterface>
     */
    protected string $entityForm;
    protected bool $pagination;

    /**
     * @param class-string<object>            $entityName
     * @param class-string<FormTypeInterface> $entityForm
     */
    protected function configureCrud(
        string $entityName,
        string $entityIdentifier = 'id',
        string $entityForm = FormType::class,
        bool $pagination = true,
    ): void {
        $this->entityName = $entityName;
        $this->entityIdentifier = $entityIdentifier;
        $this->entityForm = $entityForm;
        $this->pagination = $pagination;
    }

    protected function createEntityQueryBuilder(string $alias): QueryBuilder
    {
        $er = $this->entityManager->getRepository($this->entityName);

        return $er instanceof GenericEntityRepository
            ? $er->createQueryBuilderWithOrderBy($alias)
            : $er->createQueryBuilder($alias);
    }

    /**
     * @param mixed ...$args
     */
    protected function createEntity(...$args): object
    {
        $er = $this->entityManager->getRepository($this->entityName);

        return $er instanceof GenericEntityRepository
            ? $er->createNew($args)
            : (new \ReflectionClass($er->getClassName()))->newInstanceArgs($args);
    }

    protected function findEntity(string $_id): object
    {
        $er = $this->entityManager->getRepository($this->entityName);

        return $er->findOneBy([$this->entityIdentifier => $_id])
            ?? throw new NotFoundHttpException('Not Found');
    }

    protected function createEntityForm(object $data, array $options = []): FormInterface
    {
        return $this->formFactory->create($this->entityForm, $data, $options);
    }

    /**
     * @param object|class-string $objectOrClass
     */
    protected static function generateAlias(object|string $objectOrClass): string
    {
        $ref = new \ReflectionClass($objectOrClass);
        /** @var string */
        $class = preg_replace('/([a-z])([A-Z])/', '$1_$2', $ref->getShortName());

        return strtolower($class);
    }
}
