<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\Repositories;

use Adhoc\HighCohesion\Model\Order;
use Adhoc\HighCohesion\Model\OrderCollection;
use Adhoc\HighCohesion\Model\Address;
use Adhoc\HighCohesion\Model\LineItem;
use Adhoc\HighCohesion\Model\LineItemCollection;
use Adhoc\HighCohesion\DataSource\DataSourceInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly DataSourceInterface $dataSource
    ) {}

    /**
     * @param string $id
     * @return Order|null
     */
    public function findById(string $id): ?Order
    {
        $allOrders = $this->dataSource->getAll();
        
        foreach ($allOrders as $orderData) {
            if ($orderData['order_number'] === $id) {
                return $this->mapToOrder($orderData);
            }
        }
        
        return null;
    }

    /**
     * Get all orders as a collection
     * Uses streaming if the data source supports it for better memory efficiency
     */
    public function findAll(): OrderCollection
    {
        $orders = [];
        
        if ($this->dataSource->supportsStreaming()) {
            // Use streaming for memory efficiency with large datasets
            foreach ($this->dataSource->stream() as $orderData) {
                $orders[] = $this->mapToOrder($orderData);
            }
        } else {
            // Fall back to loading all data at once
            $data = $this->dataSource->getAll();
            $orders = array_map(
                fn(array $orderData) => $this->mapToOrder($orderData),
                $data
            );
        }
        
        return new OrderCollection($orders);
    }
    
    private function mapToOrder(array $data): Order
    {
        $lineItems = array_map(
            fn(array $item) => new LineItem(
                sku: $item['sku'],
                title: $item['title'],
                quantity: $item['quantity'],
                pricePence: (int)($item['price'] * 100),
                totalPence: (int)($item['total'] * 100)
            ),
            $data['line_items']
        );
        
        return new Order(
            orderNumber: $data['order_number'],
            title: $data['title'],
            currency: $data['currency'],
            totalPence: (int)($data['total'] * 100),
            shippingAddress: new Address(
                address1: $data['shippingAddress']['address1'],
                town: $data['shippingAddress']['town'],
                city: $data['shippingAddress']['city'],
                countryCode: $data['shippingAddress']['country_code'],
                zip: $data['shippingAddress']['zip']
            ),
            lineItems: new LineItemCollection($lineItems)
        );
    }
}
