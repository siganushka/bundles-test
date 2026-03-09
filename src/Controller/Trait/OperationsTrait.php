<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
    private string $entityFqcn;

    /**
     * @var class-string<FormTypeInterface>
     */
    private string $entityForm;

    private string $entityIdentifier;
    private string $controllerAlias;
    private string $templateAlias;
    private array $serializationCollectionContext;
    private array $serializationItemContext;

    /**
     * @param class-string<object>                 $entityFqcn
     * @param class-string<FormTypeInterface>|null $entityForm
     */
    protected function configureCrud(
        string $entityFqcn,
        ?string $entityForm = null,
        ?string $entityIdentifier = null,
        ?string $controllerAlias = null,
        ?string $templateAlias = null,
        ?array $serializationCollectionContext = null,
        ?array $serializationItemContext = null,
    ): void {
        $this->entityFqcn = $entityFqcn;
        $this->entityForm = $entityForm ?? FormType::class;
        $this->entityIdentifier = $entityIdentifier ?? 'id';
        $this->controllerAlias = $controllerAlias ?? str_replace(['_controller', '_'], '', self::generateAlias($this));
        $this->templateAlias = $templateAlias ?? str_replace('_controller', '', self::generateAlias($this));
        $this->serializationCollectionContext = $serializationCollectionContext ?? [AbstractNormalizer::GROUPS => \sprintf('%s:collection', self::generateAlias($entityFqcn))];
        $this->serializationItemContext = $serializationItemContext ?? [AbstractNormalizer::GROUPS => \sprintf('%s:item', self::generateAlias($entityFqcn))];
    }

    private function createEntityQueryBuilder(string $alias): QueryBuilder
    {
        $er = $this->entityManager->getRepository($this->entityFqcn);

        return $er instanceof GenericEntityRepository
            ? $er->createQueryBuilderWithOrderBy($alias)
            : $er->createQueryBuilder($alias);
    }

    private function createEntity(): object
    {
        $er = $this->entityManager->getRepository($this->entityFqcn);

        return $er instanceof GenericEntityRepository
            ? $er->createNew()
            : (new \ReflectionClass($er->getClassName()))->newInstance();
    }

    private function findEntity(string $_id): object
    {
        $er = $this->entityManager->getRepository($this->entityFqcn);

        return $er->findOneBy([$this->entityIdentifier => $_id])
            ?? throw new NotFoundHttpException('Not Found');
    }

    private function createEntityForm(object $data, array $options = []): FormInterface
    {
        return $this->formFactory->create($this->entityForm, $data, $options);
    }

    /**
     * @param object|class-string $objectOrClass
     */
    private static function generateAlias(object|string $objectOrClass): string
    {
        $ref = new \ReflectionClass($objectOrClass);
        /** @var string */
        $class = preg_replace('/([a-z])([A-Z])/', '$1_$2', $ref->getShortName());

        return strtolower($class);
    }
}
