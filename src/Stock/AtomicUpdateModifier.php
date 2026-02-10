<?php

declare(strict_types=1);

namespace App\Stock;

use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\OrderBundle\Entity\Order;
use Siganushka\OrderBundle\Exception\OutOfStockException;
use Siganushka\OrderBundle\Model\StockableInterface;
use Siganushka\OrderBundle\Stock\OrderStockModifierInterface;

class AtomicUpdateModifier implements OrderStockModifierInterface
{
    public const INCREMENT = 1;
    public const DECREMENT = 2;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function increment(Order $order): void
    {
        $this->modifiy($order, self::INCREMENT);
    }

    public function decrement(Order $order): void
    {
        $this->modifiy($order, self::DECREMENT);
    }

    private function modifiy(Order $order, int $action): void
    {
        $assignment = match ($action) {
            self::INCREMENT => 'entity.stock + :quantity',
            self::DECREMENT => 'entity.stock - :quantity',
            default => throw new \UnhandledMatchError('The argument "action" is not valid.'),
        };

        foreach ($order->getItems() as $item) {
            $subject = $item->getSubject();
            if (!$subject instanceof StockableInterface) {
                continue;
            }

            $stock = $subject->availableStock();
            if (null === $stock) {
                continue;
            }

            $queryBuilder = $this->entityManager->createQueryBuilder()
                ->update($subject::class, 'entity')
                ->set('entity.stock', $assignment)
                ->where('entity.id = :id')
                ->setParameter('id', $subject->getId(), ParameterType::INTEGER)
                ->setParameter('quantity', $item->getQuantity(), ParameterType::INTEGER)
            ;

            if (self::DECREMENT === $action) {
                $queryBuilder->andWhere('entity.stock >= :quantity');
            }

            $query = $queryBuilder->getQuery();
            if (!$query->execute()) {
                throw new OutOfStockException($subject, $stock, $item->getQuantity());
            }
        }
    }
}
