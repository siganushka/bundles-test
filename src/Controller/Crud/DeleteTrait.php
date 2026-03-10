<?php

declare(strict_types=1);

namespace App\Controller\Crud;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Translation\TranslatableMessage;

trait DeleteTrait
{
    use WebOperationsTrait;

    #[Route('/{_id<\d+>}/delete', methods: 'GET')]
    public function delete(Request $request, CsrfTokenManagerInterface $tokenManager, UrlGeneratorInterface $urlGenerator, string $_id): Response
    {
        $entity = $this->findEntity($_id);

        $token = new CsrfToken('delete'.$_id, $request->query->getString('_token'));
        if (!$tokenManager->isTokenValid($token)) {
            throw new AccessDeniedHttpException('Invalid csrf token.');
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        $session = $request->getSession();
        if ($session instanceof FlashBagAwareSessionInterface) {
            $metadata = $this->entityManager->getClassMetadata($entity::class);
            $session->getFlashBag()->add('success', new TranslatableMessage(
                \sprintf('Entity %s deleted successfully!', $entity::class),
                ['%_id%' => $metadata->getFieldValue($entity, $metadata->getSingleIdentifierFieldName())],
            ));
        }

        $route = \sprintf('app_%s_index', $this->getControllerAlias());
        $url = $urlGenerator->generate($route, []);

        return new RedirectResponse($url, Response::HTTP_SEE_OTHER);
    }
}
