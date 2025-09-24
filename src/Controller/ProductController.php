<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\GenericBundle\Dto\PaginationDto;
use Siganushka\ProductBundle\Form\ProductOptionType;
use Siganushka\ProductBundle\Form\ProductOptionValueType;
use Siganushka\ProductBundle\Form\ProductType;
use Siganushka\ProductBundle\Form\ProductVariantCollectionType;
use Siganushka\ProductBundle\Form\ProductVariantType;
use Siganushka\ProductBundle\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    public function __construct(protected readonly ProductRepository $repository)
    {
    }

    #[Route('/products')]
    public function index(PaginatorInterface $paginator, #[MapQueryString] PaginationDto $dto): Response
    {
        $queryBuilder = $this->repository->createQueryBuilderWithOrderBy('p');
        $pagination = $paginator->paginate($queryBuilder, $dto->page, $dto->size);

        return $this->render('product/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/products/new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->repository->createNew();
        $combinable = $request->query->has('combinable');

        $form = $this->createForm(ProductType::class, $entity, compact('combinable'));
        $form->add('submit', SubmitType::class, ['label' => 'generic.submit']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($entity);
            $entityManager->flush();

            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/products/{id<\d+>}/edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->repository->find($id)
            ?? throw $this->createNotFoundException();

        $form = $this->createForm(ProductType::class, $entity);
        $form->add('save', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/products/{id<\d+>}/delete')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->repository->find($id)
            ?? throw $this->createNotFoundException();

        $entityManager->remove($entity);
        $entityManager->flush();

        $this->addFlash('success', 'The resource has been deleted successfully!');

        return $this->redirectToRoute('app_product_index');
    }

    #[Route('/products/{id<\d+>}/variants')]
    public function variants(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->repository->find($id)
            ?? throw $this->createNotFoundException();

        $form = $this->createForm(ProductVariantCollectionType::class, $entity);
        $form->add('submit', SubmitType::class, ['label' => 'generic.submit']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('app_product_variants', ['id' => $entity->getId()]);
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/products/ProductType')]
    public function ProductType(Request $request): Response
    {
        $form = $this->createForm(ProductType::class)
            ->add('submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/products/ProductOptionType')]
    public function ProductOptionType(Request $request): Response
    {
        $form = $this->createForm(ProductOptionType::class)
            ->add('submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/products/ProductOptionValueType')]
    public function ProductOptionValueType(Request $request): Response
    {
        $form = $this->createForm(ProductOptionValueType::class)
            ->add('submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/products/ProductVariantCollectionType')]
    public function ProductVariantCollectionType(Request $request): Response
    {
        $entity = $this->repository->createNew();

        $form = $this->createForm(ProductVariantCollectionType::class, $entity)
            ->add('submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/products/ProductVariantType')]
    public function ProductVariantType(Request $request): Response
    {
        $form = $this->createForm(ProductVariantType::class)
            ->add('submit', SubmitType::class, ['label' => 'generic.submit'])
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
