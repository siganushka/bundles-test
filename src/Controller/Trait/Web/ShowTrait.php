<?php

declare(strict_types=1);

namespace App\Controller\Trait\Web;

use App\Controller\Trait\HttpOperationTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

trait ShowTrait
{
    use HttpOperationTrait;

    #[Route('/{_id}')]
    public function show(EntityManagerInterface $em, Environment $twig, string $_id): Response
    {
        $entity = $this->findEntity($em, $_id);

        $template = \sprintf('%s/%s.html.twig', $this->templateAlias, __FUNCTION__);
        $content = $twig->render($template, compact('entity'));

        return new Response($content);
    }
}
