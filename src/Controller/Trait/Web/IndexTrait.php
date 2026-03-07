<?php

declare(strict_types=1);

namespace App\Controller\Trait\Web;

use App\Controller\Trait\HttpOperationTrait;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

trait IndexTrait
{
    use HttpOperationTrait;

    #[Route(methods: 'GET')]
    public function index(Environment $twig, PaginatorInterface $paginator): Response
    {
        $qb = $this->createEntityQueryBuilder('entity');
        $pagination = $paginator->paginate($qb);

        $template = \sprintf('%s/%s.html.twig', $this->templateAlias, __FUNCTION__);
        $content = $twig->render($template, compact('pagination'));

        return new Response($content);
    }
}
