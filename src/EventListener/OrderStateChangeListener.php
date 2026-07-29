<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsEntityListener(Events::preUpdate, entity: Order::class)]
class OrderStateChangeListener
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function __invoke(Order $entity, PreUpdateEventArgs $event): void
    {
        if (!$event->hasChangedField('state')) {
            return;
        }

        $message = json_encode([
            'number' => $entity->getNumber(),
            'state' => $entity->getState()->value,
            'theme' => $entity->getState()->theme(),
            'label' => $entity->getState()->trans($this->translator),
        ], \JSON_THROW_ON_ERROR);

        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $redis->publish('sse', $message);
        $redis->close();
    }
}
