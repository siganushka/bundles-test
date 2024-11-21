<?php

declare(strict_types=1);

namespace App\EventListener;

use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Enum\OrderState;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;

class OrderStateFlowListener implements EventSubscriberInterface
{
    public function onGuard(GuardEvent $event): void
    {
        /** @var Order */
        $subject = $event->getSubject();
        if ($subject->isFree() && OrderState::Processing === $subject->getState()) {
            $event->setBlocked(true);
        }
    }

    public function onTransition(TransitionEvent $event): void
    {
        /** @var Order */
        $subject = $event->getSubject();
        if ($subject->isFree()) {
            $marking = $event->getMarking();
            $marking->mark(OrderState::Processing->value);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            GuardEvent::getName('order_state_flow', 'reset') => 'onGuard',
            TransitionEvent::getName('order_state_flow', 'reset') => 'onTransition',
        ];
    }
}
