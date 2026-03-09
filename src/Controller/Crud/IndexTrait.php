<?php

declare(strict_types=1);

namespace App\Controller\Crud;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

trait IndexTrait
{
    use WebOperationsTrait;

    #[Route(methods: 'GET')]
    public function index(Environment $twig, PaginatorInterface $paginator): Response
    {
        $qb = $this->createEntityQueryBuilder('entity');
        $pagination = $paginator->paginate($qb);

        $template = \sprintf('%s/%s.html.twig', $this->getTemplateAlias(), __FUNCTION__);
        $content = $twig->render($template, compact('pagination'));

        return new Response($content);
    }
}
