<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\Model;

class OrderCollection
{
    /**
     * @var Order[]
     */
    private array $orders = [];

    /**
     * @param Order[] $orders
     */
    public function __construct(array $orders = [])
    {
        foreach ($orders as $order) {
            $this->add($order);
        }
    }

    /**
     * Add an order to the collection
     */
    public function add(Order $order): void
    {
        $this->orders[] = $order;
    }

    /**
     * Get all orders in the collection
     * 
     * @return Order[]
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    /**
     * Get the count of orders
     */
    public function count(): int
    {
        return count($this->orders);
    }

    /**
     * Check if collection is empty
     */
    public function isEmpty(): bool
    {
        return empty($this->orders);
    }
}
