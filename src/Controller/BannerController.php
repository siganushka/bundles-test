<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\BannerBundle\Form\BannerType;
use Siganushka\BannerBundle\Repository\BannerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/banners')]
class BannerController extends AbstractController
{
    protected BannerRepository $bannerRepository;

    public function __construct(BannerRepository $bannerRepository)
    {
        $this->bannerRepository = $bannerRepository;
    }

    #[Route]
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

    #[Route('/new')]
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

    #[Route('/{id<\d+>}/edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->bannerRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Resource #%d not found.', $id));
        }

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

    #[Route('/{id<\d+>}/delete')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->bannerRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Resource #%d not found.', $id));
        }

        try {
            $entityManager->remove($entity);
            $entityManager->flush();

            $this->addFlash('success', sprintf('Resource #%s has been deleted!', $id));
        } catch (ForeignKeyConstraintViolationException $th) {
            $this->addFlash('danger', 'The associated data can be deleted if it is not empty!');
        }

        return $this->redirectToRoute('app_banner_index');
    }

    #[Route('/BannerType')]
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
