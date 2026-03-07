<?php

declare(strict_types=1);

namespace App\Controller\Trait\Web;

use App\Controller\Trait\HttpOperationTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

trait NewTrait
{
    use HttpOperationTrait;

    #[Route('/new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
    ): Response {
        $entity = $this->createEntity();

        $form = $this->createEntityForm($entity);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entity);
            $em->flush();

            $route = \sprintf('app_%s_index', $this->controllerAlias);
            $url = $urlGenerator->generate($route, []);

            return new RedirectResponse($url, Response::HTTP_SEE_OTHER);
        }

        $template = \sprintf('%s/form.html.twig', $this->templateAlias);
        $content = $twig->render($template, ['form' => $form->createView()]);

        return new Response($content);
    }
}
