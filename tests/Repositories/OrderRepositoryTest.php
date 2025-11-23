<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\Tests\Repositories;

use Adhoc\HighCohesion\DataSource\DataSourceInterface;
use Adhoc\HighCohesion\Model\Order;
use Adhoc\HighCohesion\Model\OrderCollection;
use Adhoc\HighCohesion\Repositories\OrderRepository;
use PHPUnit\Framework\TestCase;

class OrderRepositoryTest extends TestCase
{
    private function createMockDataSource(array $data): DataSourceInterface
    {
        $mock = $this->createMock(DataSourceInterface::class);
        
        $mock->method('getAll')
            ->willReturn($data);
        
        $mock->method('getOne')
            ->willReturnCallback(fn($id) => $data[$id] ?? null);
        
        return $mock;
    }

    private function getSampleOrderData(): array
    {
        return [
            '#1001' => [
                'order_number' => '#1001',
                'title' => '#1001-R1',
                'currency' => 'GBP',
                'total' => 315,
                'shippingAddress' => [
                    'address1' => '1 Main Street',
                    'town' => 'Test Town',
                    'city' => 'London',
                    'country_code' => 'UK',
                    'zip' => 'L1 1AA'
                ],
                'line_items' => [
                    [
                        'sku' => 'snow-sunglasses',
                        'title' => 'Snow Sunglasses',
                        'quantity' => 2,
                        'price' => 110,
                        'total' => 220
                    ]
                ]
            ],
            '#1002' => [
                'order_number' => '#1002',
                'title' => '#1002-R1',
                'currency' => 'USD',
                'total' => 500,
                'shippingAddress' => [
                    'address1' => '2 Second Street',
                    'town' => 'Other Town',
                    'city' => 'New York',
                    'country_code' => 'US',
                    'zip' => '10001'
                ],
                'line_items' => []
            ]
        ];
    }

    public function testFindByIdReturnsOrder(): void
    {
        $dataSource = $this->createMockDataSource($this->getSampleOrderData());
        $repository = new OrderRepository($dataSource);

        $order = $repository->findById('#1001');

        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame('#1001', $order->orderNumber);
        $this->assertSame('#1001-R1', $order->title);
        $this->assertSame('GBP', $order->currency);
        $this->assertSame(31500, $order->totalPence);
    }

    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $dataSource = $this->createMockDataSource($this->getSampleOrderData());
        $repository = new OrderRepository($dataSource);

        $order = $repository->findById('#9999');

        $this->assertNull($order);
    }

    public function testFindAllReturnsOrderCollection(): void
    {
        $dataSource = $this->createMockDataSource($this->getSampleOrderData());
        $repository = new OrderRepository($dataSource);

        $collection = $repository->findAll();

        $this->assertInstanceOf(OrderCollection::class, $collection);
        $this->assertSame(2, $collection->count());
    }

    public function testFindAllMapsDataCorrectly(): void
    {
        $dataSource = $this->createMockDataSource($this->getSampleOrderData());
        $repository = new OrderRepository($dataSource);

        $collection = $repository->findAll();
        $orders = $collection->getOrders();

        $this->assertCount(2, $orders);
        
        $firstOrder = $orders[0];
        $this->assertInstanceOf(Order::class, $firstOrder);
        $this->assertSame('#1001', $firstOrder->orderNumber);
        
        $secondOrder = $orders[1];
        $this->assertInstanceOf(Order::class, $secondOrder);
        $this->assertSame('#1002', $secondOrder->orderNumber);
    }

    public function testRepositoryMapsShippingAddressCorrectly(): void
    {
        $dataSource = $this->createMockDataSource($this->getSampleOrderData());
        $repository = new OrderRepository($dataSource);

        $order = $repository->findById('#1001');

        $this->assertSame('1 Main Street', $order->shippingAddress->address1);
        $this->assertSame('Test Town', $order->shippingAddress->town);
        $this->assertSame('London', $order->shippingAddress->city);
        $this->assertSame('UK', $order->shippingAddress->countryCode);
        $this->assertSame('L1 1AA', $order->shippingAddress->zip);
    }

    public function testRepositoryMapsLineItemsCorrectly(): void
    {
        $dataSource = $this->createMockDataSource($this->getSampleOrderData());
        $repository = new OrderRepository($dataSource);

        $order = $repository->findById('#1001');

        $this->assertSame(1, $order->lineItems->count());
        
        $items = $order->lineItems->getItems();
        $firstItem = $items[0];
        
        $this->assertSame('snow-sunglasses', $firstItem->sku);
        $this->assertSame('Snow Sunglasses', $firstItem->title);
        $this->assertSame(2, $firstItem->quantity);
        $this->assertSame(11000, $firstItem->pricePence);
        $this->assertSame(22000, $firstItem->totalPence);
    }
}
