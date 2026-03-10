<?php

declare(strict_types=1);

namespace App\Controller\Crud;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

trait DeleteTrait
{
    use WebOperationsTrait;

    #[Route('/{_id<\d+>}/delete', methods: 'GET')]
    public function delete(Request $request, CsrfTokenManagerInterface $csrfTokenManager, UrlGeneratorInterface $urlGenerator, string $_id): Response
    {
        $entity = $this->findEntity($_id);

        $csrfToken = new CsrfToken('delete'.$_id, $request->query->getString('_token'));
        if ($csrfTokenManager->isTokenValid($csrfToken)) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        } else {
            $this->addFlash('danger', 'Invalid csrf token.');
        }

        $route = \sprintf('app_%s_index', $this->getControllerAlias());
        $url = $urlGenerator->generate($route, []);

        return new RedirectResponse($url, Response::HTTP_SEE_OTHER);
    }
}
