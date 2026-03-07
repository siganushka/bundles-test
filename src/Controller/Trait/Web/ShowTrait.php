<?php

declare(strict_types=1);

namespace App\Controller\Trait\Web;

use App\Controller\Trait\OperationsTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

trait ShowTrait
{
    use OperationsTrait;

    #[Route('/{_id<\d+>}', methods: 'GET')]
    public function show(Environment $twig, string $_id): Response
    {
        $entity = $this->findEntity($_id);

        $template = \sprintf('%s/%s.html.twig', $this->templateAlias, __FUNCTION__);
        $content = $twig->render($template, compact('entity'));

        return new Response($content);
    }
}
