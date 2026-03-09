<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Crud\DeleteTrait;
use App\Controller\Crud\EditTrait;
use App\Controller\Crud\IndexTrait;
use App\Controller\Crud\NewTrait;
use App\Controller\Crud\ShowTrait;
use App\Entity\User;
use Siganushka\UserBundle\Form\ChangePasswordType;
use Siganushka\UserBundle\Form\RegistrationType;
use Siganushka\UserBundle\Form\ResetPasswordType;
use Siganushka\UserBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users')]
class UserController extends AbstractController
{
    use IndexTrait;
    use NewTrait;
    use ShowTrait;
    use EditTrait;
    use DeleteTrait;

    public function __construct()
    {
        $this->configureCrud(
            entityFqcn: User::class,
            entityForm: UserType::class,
        );
    }

    #[Route('/UserType')]
    public function UserType(Request $request): Response
    {
        $form = $this->createForm(UserType::class)
            ->add('submit', SubmitType::class)
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('user/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/RegistrationType')]
    public function RegistrationType(Request $request): Response
    {
        $form = $this->createForm(RegistrationType::class)
            ->add('submit', SubmitType::class)
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('user/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/ChangePasswordType')]
    public function ChangePasswordType(Request $request): Response
    {
        $form = $this->createForm(ChangePasswordType::class)
            ->add('submit', SubmitType::class)
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dd(__METHOD__, $form->getData());
        }

        return $this->render('user/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/ResetPasswordType')]
    public function ResetPasswordType(Request $request): Response
    {
        $form = $this->createForm(ResetPasswordType::class)
            ->add('submit', SubmitType::class)
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
