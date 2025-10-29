<?php

declare(strict_types=1);

namespace App\EventListener;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Enum\OrderState;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;

class OrderStateMachineListener implements EventSubscriberInterface
{
    public function onGuard(GuardEvent $event): void
    {
        $subject = $event->getSubject();
        if (!$subject instanceof Order) {
            return;
        }

        if (OrderState::Processing === $subject->getState() && $subject->getTotal() <= 0) {
            $event->setBlocked(true);
        }
    }

    public function onTransition(TransitionEvent $event): void
    {
        $subject = $event->getSubject();
        if (!$subject instanceof Order) {
            return;
        }

        if ($subject->getTotal() <= 0) {
            $marking = $event->getMarking();
            $marking->mark(OrderState::Processing->value);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            GuardEvent::getName('order', 'reset') => 'onGuard',
            TransitionEvent::getName('order', 'reset') => 'onTransition',
        ];
    }
}
