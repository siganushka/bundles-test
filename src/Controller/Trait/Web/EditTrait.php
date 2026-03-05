<?php

declare(strict_types=1);

namespace App\Controller\Trait\Web;

use App\Controller\Trait\HttpOperationTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

trait EditTrait
{
    use HttpOperationTrait;

    #[Route('/{_id<\d+>}/edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        FormFactoryInterface $factory,
        string $_id,
    ): Response {
        $entity = $this->findEntity($em, $_id);

        $form = $factory->create($this->entityForm, $entity);
        $form->add('submit', SubmitType::class, ['label' => 'generic.save']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
