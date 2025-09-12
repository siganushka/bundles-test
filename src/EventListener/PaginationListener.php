<?php

declare(strict_types=1);

namespace App\EventListener;

use Knp\Component\Pager\Event\BeforeEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener('knp_pager.before', priority: -8)]
class PaginationListener
{
    public function __invoke(BeforeEvent $event): void
    {
    }
}
