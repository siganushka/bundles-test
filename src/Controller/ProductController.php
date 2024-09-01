<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products')]
class ProductController extends AbstractController
{
    public function __construct(protected readonly ProductRepository $productRepository)
    {
    }

    #[Route]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->productRepository->createQueryBuilder('p');

        $page = $request->query->getInt('page', 1);
        $size = $request->query->getInt('size', 10);

        $pagination = $paginator->paginate($queryBuilder, $page, $size);

        return $this->render('product/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->productRepository->createNew();

        $form = $this->createForm(ProductType::class, $entity);
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

    #[Route('/{id<\d+>}/edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->productRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException(\sprintf('Resource #%d not found.', $id));
        }

        $form = $this->createForm(ProductType::class, $entity);
        $form->add('save', SubmitType::class, ['label' => 'generic.save']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Your changes were saved!');

                return $this->redirectToRoute('app_product_index');
            } catch (ForeignKeyConstraintViolationException $th) {
                $this->addFlash('danger', 'The associated data can be deleted if it is not empty!');

                return $this->redirectToRoute('app_product_edit', compact('id'));
            }
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}/delete')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->productRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException(\sprintf('Resource #%d not found.', $id));
        }

        try {
            $entityManager->remove($entity);
            $entityManager->flush();

            $this->addFlash('success', \sprintf('Resource #%s has been deleted!', $id));

            return $this->redirectToRoute('app_product_index');
        } catch (ForeignKeyConstraintViolationException $th) {
            $this->addFlash('danger', 'The associated data can be deleted if it is not empty!');

            return $this->redirectToRoute('app_product_index');
        }
    }

    #[Route('/{id<\d+>}/variants')]
    public function variants(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException(\sprintf('Resource #%d not found.', $id));
        }

        $form = $this->createForm(ProductVariantCollectionType::class, $product);
        $form->add('submit', SubmitType::class, ['label' => 'generic.save']);
        $form->handleRequest($request);
        // dd($form->createView());

        if ($form->isSubmitted() && $form->isValid()) {
            // dd(__METHOD__, $product->getVariants()->toArray());

            $entityManager->flush();
            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/ProductType')]
    public function ProductType(Request $request): Response
    {
        $form = $this->createForm(ProductType::class)
            ->add('Submit', SubmitType::class, ['label' => 'generic.submit'])
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
            ->add('Submit', SubmitType::class, ['label' => 'generic.submit'])
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
            ->add('Submit', SubmitType::class, ['label' => 'generic.submit'])
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
        $entity = $this->productRepository->createNew();

        $form = $this->createForm(ProductVariantCollectionType::class, $entity)
            ->add('Submit', SubmitType::class, ['label' => 'generic.submit'])
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
            ->add('Submit', SubmitType::class, ['label' => 'generic.submit'])
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
