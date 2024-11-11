<?php

declare(strict_types=1);

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Entity\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;

class OrderLockInventoryListener implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TransitionEvent::getName('order_state', null) => 'onTransitionEvent',
        ];
    }

    public function onTransitionEvent(TransitionEvent $event): void
    {
        $transition = $event->getTransition();
        if (!$transition || !\in_array($transition->getName(), ['refund', 'cancel'], true)) {
            return;
        }

        $this->entityManager->beginTransaction();

        /** @var Order */
        $order = $event->getSubject();
        foreach ($order->getItems() as $item) {
            $subject = $item->getSubject();
            $quantity = $item->getQuantity();
            if (null === $subject || null === $quantity) {
                continue;
            }

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->update($subject::class, 't');
            $queryBuilder->set('t.inventory', 't.inventory + :quantity');
            $queryBuilder->where('t.id = :id');
            $queryBuilder->setParameter('id', $subject->getId());
            $queryBuilder->setParameter('quantity', $quantity);

            $query = $queryBuilder->getQuery();
            if (!$query->execute()) {
                throw new \RuntimeException('Unable to lock inventory.');
            }
        }

        $this->entityManager->commit();
    }
}
