<?php

declare(strict_types=1);

namespace App\Controller;

use Siganushka\MediaBundle\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\InMemoryUser;

class AdminController extends AbstractController
{
    public function __construct(protected readonly MediaRepository $repository)
    {
    }

    #[Route('/admin/dashboard')]
    public function dashboard(?Security $security): Response
    {
        $this->addFlash('success', 'Welcome to SiganushkaAdminBundle!');

        try {
            // The "symfony/security-bundle" is optional
            $security && $security->login(new InMemoryUser('Siganushka', '123456'));
        } catch (\Throwable $th) {
            dd($th);
        }

        return $this->render('admin/dashboard.html.twig');
    }
}
