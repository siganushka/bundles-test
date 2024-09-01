<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\ProductBundle\Form\ProductVariantType;
use Siganushka\ProductBundle\Repository\ProductVariantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product-variants')]
class ProductVariantController extends AbstractController
{
    public function __construct(protected readonly ProductVariantRepository $productVariantRepository)
    {
    }

    #[Route]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->productVariantRepository->createQueryBuilder('v');

        $page = $request->query->getInt('page', 1);
        $size = $request->query->getInt('size', 10);

        $pagination = $paginator->paginate($queryBuilder, $page, $size);

        return $this->render('product-variant/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/{id<\d+>}/edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->productVariantRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException(\sprintf('Resource #%d not found.', $id));
        }

        $form = $this->createForm(ProductVariantType::class, $entity);
        $form->add('submit', SubmitType::class, ['label' => 'generic.submit']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_productvariant_index');
        }

        return $this->render('product-variant/form.html.twig', [
            'form' => $form,
        ]);
    }
}
