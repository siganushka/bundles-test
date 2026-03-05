<?php

declare(strict_types=1);

namespace App\Controller\Trait\Web;

use App\Controller\Trait\HttpOperationTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

trait DeleteTrait
{
    use HttpOperationTrait;

    #[Route('/{_id}/delete')]
    public function delete(
        Request $request,
        EntityManagerInterface $em,
        CsrfTokenManagerInterface $csrfTokenManager,
        UrlGeneratorInterface $urlGenerator,
        string $_id,
    ): Response {
        $entity = $this->findEntity($em, $_id);

        $csrfToken = new CsrfToken('delete'.$_id, $request->getPayload()->getString('_token'));
        if ($csrfTokenManager->isTokenValid($csrfToken)) {
            $em->remove($entity);
            $em->flush();
        }

        $route = \sprintf('app_%s_index', $this->controllerAlias);
        $url = $urlGenerator->generate($route, []);

        return new RedirectResponse($url, Response::HTTP_SEE_OTHER);
    }
}
