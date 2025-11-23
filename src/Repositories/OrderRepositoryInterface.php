<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\Repositories;

use Adhoc\HighCohesion\Model\Order;
use Adhoc\HighCohesion\Model\OrderCollection;

interface OrderRepositoryInterface
{
    /**
     * Get all orders as a collection
     */
    public function findAll(): OrderCollection;
    
    /**
     * @param string $id
     * @return Order|null
     */
    public function findById(string $id): ?Order;
}
