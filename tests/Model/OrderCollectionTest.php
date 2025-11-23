<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\Tests\Model;

use Adhoc\HighCohesion\Model\Order;
use Adhoc\HighCohesion\Model\OrderCollection;
use Adhoc\HighCohesion\Model\Address;
use Adhoc\HighCohesion\Model\LineItemCollection;
use PHPUnit\Framework\TestCase;

class OrderCollectionTest extends TestCase
{
    private function createOrder(string $orderNumber): Order
    {
        return new Order(
            orderNumber: $orderNumber,
            title: $orderNumber . '-R1',
            currency: 'GBP',
            totalPence: 10000,
            shippingAddress: new Address(
                address1: '123 Main St',
                town: 'Test Town',
                city: 'London',
                countryCode: 'UK',
                zip: 'L1 1AA'
            ),
            lineItems: new LineItemCollection([])
        );
    }

    public function testCanCreateEmptyCollection(): void
    {
        $collection = new OrderCollection();

        $this->assertInstanceOf(OrderCollection::class, $collection);
        $this->assertTrue($collection->isEmpty());
        $this->assertSame(0, $collection->count());
    }

    public function testCanCreateCollectionWithOrders(): void
    {
        $orders = [
            $this->createOrder('#1001'),
            $this->createOrder('#1002'),
            $this->createOrder('#1003'),
        ];

        $collection = new OrderCollection($orders);

        $this->assertCount(3, $collection->getOrders());
        $this->assertSame(3, $collection->count());
        $this->assertFalse($collection->isEmpty());
    }

    public function testCanAddOrderToCollection(): void
    {
        $collection = new OrderCollection();
        $order = $this->createOrder('#1001');

        $this->assertSame(0, $collection->count());

        $collection->add($order);

        $this->assertSame(1, $collection->count());
        $this->assertFalse($collection->isEmpty());
    }

    public function testGetOrdersReturnsArray(): void
    {
        $order1 = $this->createOrder('#1001');
        $order2 = $this->createOrder('#1002');

        $collection = new OrderCollection([$order1, $order2]);
        $orders = $collection->getOrders();

        $this->assertIsArray($orders);
        $this->assertCount(2, $orders);
        $this->assertSame($order1, $orders[0]);
        $this->assertSame($order2, $orders[1]);
    }

    public function testCanIterateOverCollection(): void
    {
        $orders = [
            $this->createOrder('#1001'),
            $this->createOrder('#1002'),
            $this->createOrder('#1003'),
        ];

        $collection = new OrderCollection($orders);
        
        $count = 0;
        foreach ($collection->getOrders() as $order) {
            $this->assertInstanceOf(Order::class, $order);
            $count++;
        }

        $this->assertSame(3, $count);
    }
}
