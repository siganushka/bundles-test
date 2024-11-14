<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\OrderCancelledMessage;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Repository\OrderRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
final class OrderCancelledMessageHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orderRepository,
        private readonly WorkflowInterface $orderState)
    {
    }

    public function __invoke(OrderCancelledMessage $message): void
    {
        $entity = $this->orderRepository->findOneByNumber($message->getNumber());
        if (!$entity) {
            return;
        }

        $this->orderState->apply($entity, 'cancel');
        $this->entityManager->flush();
    }
}
