<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\Tests\Integration;

use Adhoc\HighCohesion\DataSource\DataSourceFactory;
use Adhoc\HighCohesion\Model\OrderCollection;
use Adhoc\HighCohesion\Repositories\OrderRepository;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for OrderRepository reading from actual orders.json file
 */
class OrderRepositoryIntegrationTest extends TestCase
{
    private OrderRepository $repository;
    private string $ordersFilePath;

    protected function setUp(): void
    {
        $this->ordersFilePath = __DIR__ . '/../../orders.json';
        
        if (!file_exists($this->ordersFilePath)) {
            $this->markTestSkipped('orders.json file not found');
        }

        $dataSource = DataSourceFactory::createJsonFile($this->ordersFilePath);
        $this->repository = new OrderRepository($dataSource);
    }

    public function test_can_read_orders_from_json_file(): void
    {
        $orders = $this->repository->findAll();

        $this->assertInstanceOf(OrderCollection::class, $orders);
        $this->assertFalse($orders->isEmpty());
    }

    public function test_orders_have_correct_structure(): void
    {
        $orders = $this->repository->findAll();
        $ordersList = $orders->getOrders();

        $this->assertCount(1, $ordersList);

        $order = $ordersList[0];
        $this->assertEquals('#1001', $order->orderNumber);
        $this->assertEquals('#1001-R1', $order->title);
        $this->assertEquals('GBP', $order->currency);
        $this->assertEquals(31500, $order->totalPence);
    }

    public function test_shipping_address_is_parsed_correctly(): void
    {
        $orders = $this->repository->findAll();
        $order = $orders->getOrders()[0];

        $address = $order->shippingAddress;
        $this->assertEquals('1 Main Street', $address->address1);
        $this->assertEquals('Test Town', $address->town);
        $this->assertEquals('London', $address->city);
        $this->assertEquals('UK', $address->countryCode);
        $this->assertEquals('L1 1AA', $address->zip);
    }

    public function test_line_items_are_parsed_correctly(): void
    {
        $orders = $this->repository->findAll();
        $order = $orders->getOrders()[0];

        $lineItems = $order->lineItems->getItems();
        $this->assertCount(2, $lineItems);

        $firstItem = $lineItems[0];
        $this->assertEquals('snow-sunglasses', $firstItem->sku);
        $this->assertEquals('Snow Sunglasses', $firstItem->title);
        $this->assertEquals(2, $firstItem->quantity);
        $this->assertEquals(11000, $firstItem->pricePence);
        $this->assertEquals(22000, $firstItem->totalPence);

        $secondItem = $lineItems[1];
        $this->assertEquals('forest-sunglasses', $secondItem->sku);
        $this->assertEquals('Forest Sunglasses', $secondItem->title);
        $this->assertEquals(1, $secondItem->quantity);
        $this->assertEquals(9500, $secondItem->pricePence);
        $this->assertEquals(9500, $secondItem->totalPence);
    }

    public function test_line_items_total_matches_order_total(): void
    {
        $orders = $this->repository->findAll();
        $order = $orders->getOrders()[0];

        $lineItemsTotal = $order->lineItems->getTotalValuePence();
        $this->assertEquals($order->totalPence, $lineItemsTotal);
        $this->assertEquals(31500, $lineItemsTotal);
    }

    public function test_can_find_order_by_id(): void
    {
        $order = $this->repository->findById('#1001');

        $this->assertNotNull($order);
        $this->assertEquals('#1001', $order->orderNumber);
        $this->assertEquals('#1001-R1', $order->title);
    }

    public function test_returns_null_for_non_existent_order_id(): void
    {
        $order = $this->repository->findById('#9999');

        $this->assertNull($order);
    }

    public function test_order_implements_title_interface(): void
    {
        $orders = $this->repository->findAll();
        $order = $orders->getOrders()[0];

        $this->assertEquals('#1001-R1', $order->getTitle());
    }

    public function test_formatted_total_includes_currency(): void
    {
        $orders = $this->repository->findAll();
        $order = $orders->getOrders()[0];

        $this->assertEquals('GBP 315.00', $order->getTotalFormatted());
    }
}
