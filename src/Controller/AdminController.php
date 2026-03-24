<?php

declare(strict_types=1);

namespace App\Controller;

use Siganushka\UserBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/dashboard')]
    public function dashboard(UserRepository $userRepository, ?Security $security): Response
    {
        $this->addFlash('success', 'Welcome to SiganushkaAdminBundle!');

        try {
            // The "symfony/security-bundle" is optional
            $user = $userRepository->findOneByIdentifier('siganushka');
            $security && $user && $security->login($user);
        } catch (\Throwable $th) {
            dd($th);
        }

        return $this->render('admin/dashboard.html.twig');
    }
}
