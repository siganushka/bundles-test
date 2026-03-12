<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Persistence\ManagerRegistry;
use Siganushka\OrderBundle\Enum\OrderState;
use Siganushka\OrderBundle\Repository\OrderRepository as BaseOrderRepository;

/**
 * @extends BaseOrderRepository<Order>
 */
class OrderRepository extends BaseOrderRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @return array<value-of<OrderState>, int>
     */
    public function countByStateMapping(): array
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->select('o.state, COUNT(o) as count')
            ->groupBy('o.state')
        ;

        $query = $queryBuilder->getQuery();
        $result = $query->getScalarResult();

        return array_column($result, 'count', 'state');
    }
}
