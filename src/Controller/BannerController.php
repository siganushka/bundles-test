<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\BannerBundle\Entity\Banner;
use Siganushka\BannerBundle\Form\BannerType;
use Siganushka\BannerBundle\Repository\BannerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BannerController extends AbstractController
{
    public function __construct(protected readonly BannerRepository $bannerRepository)
    {
    }

    #[Route('/banners')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->bannerRepository->createQueryBuilder('b');

        $page = $request->query->getInt('page', 1);
        $size = $request->query->getInt('size', 10);

        $pagination = $paginator->paginate($queryBuilder, $page, $size);

        return $this->render('banner/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/banners/new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->bannerRepository->createNew();
        $entity->setEnabled(true);

        $form = $this->createForm(BannerType::class, $entity);
        $form->add('submit', SubmitType::class, ['label' => 'generic.submit']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($entity);
            $entityManager->flush();

            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('app_banner_index');
        }

        return $this->render('banner/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/banners/{id<\d+>}/edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Banner $entity): Response
    {
        $form = $this->createForm(BannerType::class, $entity);
        $form->add('save', SubmitType::class, ['label' => 'generic.save']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('app_banner_index');
        }

        return $this->render('banner/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/banners/{id<\d+>}/delete')]
    public function delete(EntityManagerInterface $entityManager, Banner $entity): Response
    {
        $entityManager->remove($entity);
        $entityManager->flush();

        $this->addFlash('success', 'The resource has been deleted successfully!');

        return $this->redirectToRoute('app_banner_index');
    }

    #[Route('/banners/BannerType')]
    public function BannerType(Request $request): Response
    {
        $form = $this->createForm(BannerType::class)
            ->add('Submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('banner/form.html.twig', [
            'form' => $form,
        ]);
    }
}
