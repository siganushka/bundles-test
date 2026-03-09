<?php

declare(strict_types=1);

namespace App\Controller\Crud;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

trait ShowTrait
{
    use WebOperationsTrait;

    #[Route('/{_id<\d+>}', methods: 'GET')]
    public function show(Environment $twig, string $_id): Response
    {
        $entity = $this->findEntity($_id);

        $template = \sprintf('%s/%s.html.twig', $this->getTemplateAlias(), __FUNCTION__);
        $content = $twig->render($template, compact('entity'));

        return new Response($content);
    }
}
