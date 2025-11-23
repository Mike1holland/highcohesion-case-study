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
        $data = $this->dataSource->getOne($id);
        
        if ($data === null) {
            return null;
        }
        
        return $this->mapToOrder($data);
    }

    /**
     * Get all orders as a collection
     */
    public function findAll(): OrderCollection
    {
        $data = $this->dataSource->getAll();
        $orders = array_map(
            fn(array $orderData) => $this->mapToOrder($orderData),
            $data
        );
        return new OrderCollection($orders);
    }
    
    private function mapToOrder(array $data): Order
    {
        $lineItems = array_map(
            fn(array $item) => new LineItem(
                sku: $item['sku'],
                title: $item['title'],
                quantity: $item['quantity'],
                price: $item['price'],
                total: $item['total']
            ),
            $data['line_items']
        );
        
        return new Order(
            orderNumber: $data['order_number'],
            title: $data['title'],
            currency: $data['currency'],
            total: $data['total'],
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
