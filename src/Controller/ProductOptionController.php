<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\ProductBundle\Form\ProductOptionType;
use Siganushka\ProductBundle\Repository\ProductOptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductOptionController extends AbstractController
{
    public function __construct(protected readonly ProductOptionRepository $repository)
    {
    }

    #[Route('/product-options/{id<\d+>}/edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->repository->find($id)
            ?? throw $this->createNotFoundException();

        $form = $this->createForm(ProductOptionType::class, $entity);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product-option/form.html.twig', [
            'form' => $form,
        ]);
    }
}
