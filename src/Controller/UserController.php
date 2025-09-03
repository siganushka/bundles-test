<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Siganushka\UserBundle\Form\ChangePasswordType;
use Siganushka\UserBundle\Form\ResetPasswordType;
use Siganushka\UserBundle\Form\UserType;
use Siganushka\UserBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    public function __construct(protected readonly UserRepository $repository)
    {
    }

    #[Route('/users')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->repository->createQueryBuilderWithOrdered('u');

        $page = $request->query->getInt('page', 1);
        $size = $request->query->getInt('size', 10);

        $pagination = $paginator->paginate($queryBuilder, $page, $size);

        return $this->render('user/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/users/new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->repository->createNew();

        $form = $this->createForm(UserType::class, $entity);
        $form->add('submit', SubmitType::class, ['label' => 'generic.submit']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($entity);
            $entityManager->flush();

            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/users/{id<\d+>}/edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->repository->find($id)
            ?? throw $this->createNotFoundException();

        $form = $this->createForm(UserType::class, $entity);
        $form->add('submit', SubmitType::class, ['label' => 'generic.submit']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/users/{id<\d+>}/delete')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $entity = $this->repository->find($id)
            ?? throw $this->createNotFoundException();

        $entityManager->remove($entity);
        $entityManager->flush();

        $this->addFlash('success', 'The resource has been deleted successfully!');

        return $this->redirectToRoute('app_user_index');
    }

    #[Route('/users/UserType')]
    public function UserType(Request $request): Response
    {
        $form = $this->createForm(UserType::class)
            ->add('submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('user/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/users/ChangePasswordType')]
    public function ChangePasswordType(Request $request): Response
    {
        $form = $this->createForm(ChangePasswordType::class)
            ->add('submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('user/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/users/ResetPasswordType')]
    public function ResetPasswordType(Request $request): Response
    {
        $form = $this->createForm(ResetPasswordType::class)
            ->add('submit', SubmitType::class, ['label' => 'generic.submit'])
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('user/form.html.twig', [
            'form' => $form,
        ]);
    }
}
