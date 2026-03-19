<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Siganushka\GenericBundle\Controller\Crud\Web\DeleteTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\EditTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\IndexTrait;
use Siganushka\GenericBundle\Controller\Crud\Web\NewTrait;
use Siganushka\ProductBundle\Dto\ProductQueryDto;
use Siganushka\ProductBundle\Form\ProductOptionType;
use Siganushka\ProductBundle\Form\ProductOptionValueType;
use Siganushka\ProductBundle\Form\ProductType;
use Siganushka\ProductBundle\Form\ProductVariantCollectionType;
use Siganushka\ProductBundle\Form\ProductVariantType;
use Siganushka\ProductBundle\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Translation\TranslatableMessage;

#[Route('/products')]
class ProductController extends AbstractController
{
    use IndexTrait;
    use NewTrait;
    use EditTrait;
    use DeleteTrait;

    public function __construct(
        protected readonly ProductRepository $repository,
        protected readonly RequestStack $requestStack)
    {
        $this->configureCrud(
            entityName: Product::class,
            entityForm: ProductType::class,
            queryDtoClass: ProductQueryDto::class,
        );
    }

    protected function createEntityQueryBuilder(string $alias, ProductQueryDto $dto): QueryBuilder
    {
        return $this->repository->createQueryBuilderByDto($alias, $dto);
    }

    protected function createEntityForm(object $data, array $options = []): FormInterface
    {
        $options['combinable'] = $this->requestStack->getCurrentRequest()?->query->getBoolean('combinable');

        return $this->createForm($this->entityForm, $data, $options);
    }

    protected function isGrantedForOperation(string $operation): bool
    {
        return match ($operation) {
            self::OPERATION_CREATE,
            self::OPERATION_UPDATE => true,
            default => false,
        };
    }

    #[Route('/{id<\d+>}/variants', methods: ['GET', 'POST'])]
    public function variants(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->repository->find($id)
            ?? throw $this->createNotFoundException();

        $form = $this->createForm(ProductVariantCollectionType::class, $entity, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $message = \sprintf('Entity %s updated successfully!', $entity::class);
            $this->addFlashMessage($request, 'success', new TranslatableMessage($message, ['%_id%' => $id]));
        }

        return $this->render('product/_product_variants_form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/ProductType')]
    public function ProductType(Request $request): Response
    {
        $form = $this->createForm(ProductType::class)
            ->add('submit', SubmitType::class)
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/ProductOptionType')]
    public function ProductOptionType(Request $request): Response
    {
        $form = $this->createForm(ProductOptionType::class)
            ->add('submit', SubmitType::class)
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/ProductOptionValueType')]
    public function ProductOptionValueType(Request $request): Response
    {
        $form = $this->createForm(ProductOptionValueType::class)
            ->add('submit', SubmitType::class)
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/ProductVariantCollectionType')]
    public function ProductVariantCollectionType(Request $request): Response
    {
        $entity = $this->repository->createNew();

        $form = $this->createForm(ProductVariantCollectionType::class, $entity)
            ->add('submit', SubmitType::class)
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/ProductVariantType')]
    public function ProductVariantType(Request $request): Response
    {
        $form = $this->createForm(ProductVariantType::class)
            ->add('submit', SubmitType::class)
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }
}
