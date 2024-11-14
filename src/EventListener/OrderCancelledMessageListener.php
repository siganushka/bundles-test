<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Message\OrderCancelledMessage;
use Siganushka\OrderBundle\Event\OrderCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Workflow\WorkflowInterface;

class OrderCancelledMessageListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly WorkflowInterface $orderState)
    {
    }

    public function onOrderCreated(OrderCreatedEvent $event): void
    {
        $order = $event->getOrder();
        if (!$order->getNumber()) {
            return;
        }

        // Check by workflow transition
        if (!$this->orderState->can($order, 'cancel')) {
            return;
        }

        $message = new OrderCancelledMessage($order->getNumber());
        $envelope = (new Envelope($message))
            ->with(new DelayStamp(60 * 1000))
        ;

        $this->messageBus->dispatch($envelope);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderCreatedEvent::class => 'onOrderCreated',
        ];
    }
}
