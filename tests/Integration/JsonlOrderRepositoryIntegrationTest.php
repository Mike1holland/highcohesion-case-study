<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\Tests\Integration;

use Adhoc\HighCohesion\DataSource\DataSourceFactory;
use Adhoc\HighCohesion\Model\OrderCollection;
use Adhoc\HighCohesion\Repositories\OrderRepository;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for OrderRepository reading from JSONL file
 */
class JsonlOrderRepositoryIntegrationTest extends TestCase
{
    private OrderRepository $repository;
    private string $ordersFilePath;

    protected function setUp(): void
    {
        $this->ordersFilePath = __DIR__ . '/../../orders.jsonl';
        
        if (!file_exists($this->ordersFilePath)) {
            $this->markTestSkipped('orders.jsonl file not found');
        }

        $dataSource = DataSourceFactory::createJsonLinesFile($this->ordersFilePath);
        $this->repository = new OrderRepository($dataSource);
    }

    public function test_can_read_multiple_orders_from_jsonl_file(): void
    {
        $orders = $this->repository->findAll();

        $this->assertInstanceOf(OrderCollection::class, $orders);
        $this->assertFalse($orders->isEmpty());
        $this->assertEquals(2, $orders->count());
    }

    public function test_first_order_has_correct_data(): void
    {
        $orders = $this->repository->findAll();
        $ordersList = $orders->getOrders();

        $firstOrder = $ordersList[0];
        $this->assertEquals('#1001', $firstOrder->orderNumber);
        $this->assertEquals('#1001-R1', $firstOrder->title);
        $this->assertEquals('GBP', $firstOrder->currency);
        $this->assertEquals(31500, $firstOrder->totalPence);
    }

    public function test_second_order_has_correct_data(): void
    {
        $orders = $this->repository->findAll();
        $ordersList = $orders->getOrders();

        $secondOrder = $ordersList[1];
        $this->assertEquals('#1002', $secondOrder->orderNumber);
        $this->assertEquals('#1002-R1', $secondOrder->title);
        $this->assertEquals('USD', $secondOrder->currency);
        $this->assertEquals(42500, $secondOrder->totalPence);
    }

    public function test_second_order_shipping_address(): void
    {
        $orders = $this->repository->findAll();
        $order = $orders->getOrders()[1];

        $address = $order->shippingAddress;
        $this->assertEquals('456 Oak Avenue', $address->address1);
        $this->assertEquals('Springfield', $address->town);
        $this->assertEquals('New York', $address->city);
        $this->assertEquals('US', $address->countryCode);
        $this->assertEquals('10001', $address->zip);
    }

    public function test_second_order_has_three_line_items(): void
    {
        $orders = $this->repository->findAll();
        $order = $orders->getOrders()[1];

        $lineItems = $order->lineItems->getItems();
        $this->assertCount(3, $lineItems);

        $firstItem = $lineItems[0];
        $this->assertEquals('hiking-boots', $firstItem->sku);
        $this->assertEquals('Hiking Boots', $firstItem->title);
        $this->assertEquals(1, $firstItem->quantity);
        $this->assertEquals(25000, $firstItem->pricePence);
        $this->assertEquals(25000, $firstItem->totalPence);

        $secondItem = $lineItems[1];
        $this->assertEquals('wool-socks', $secondItem->sku);
        $this->assertEquals('Wool Socks', $secondItem->title);
        $this->assertEquals(3, $secondItem->quantity);
        $this->assertEquals(2500, $secondItem->pricePence);
        $this->assertEquals(7500, $secondItem->totalPence);

        $thirdItem = $lineItems[2];
        $this->assertEquals('water-bottle', $thirdItem->sku);
        $this->assertEquals('Water Bottle', $thirdItem->title);
        $this->assertEquals(1, $thirdItem->quantity);
        $this->assertEquals(10000, $thirdItem->pricePence);
        $this->assertEquals(10000, $thirdItem->totalPence);
    }

    public function test_second_order_line_items_total_matches(): void
    {
        $orders = $this->repository->findAll();
        $order = $orders->getOrders()[1];

        $lineItemsTotal = $order->lineItems->getTotalValuePence();
        $this->assertEquals($order->totalPence, $lineItemsTotal);
        $this->assertEquals(42500, $lineItemsTotal);
    }

    public function test_can_find_first_order_by_id(): void
    {
        $order = $this->repository->findById('#1001');

        $this->assertNotNull($order);
        $this->assertEquals('#1001', $order->orderNumber);
        $this->assertEquals('GBP', $order->currency);
    }

    public function test_can_find_second_order_by_id(): void
    {
        $order = $this->repository->findById('#1002');

        $this->assertNotNull($order);
        $this->assertEquals('#1002', $order->orderNumber);
        $this->assertEquals('USD', $order->currency);
    }

    public function test_returns_null_for_non_existent_order(): void
    {
        $order = $this->repository->findById('#9999');

        $this->assertNull($order);
    }

    public function test_both_orders_implement_title_interface(): void
    {
        $orders = $this->repository->findAll();
        $ordersList = $orders->getOrders();

        $this->assertEquals('#1001-R1', $ordersList[0]->getTitle());
        $this->assertEquals('#1002-R1', $ordersList[1]->getTitle());
    }

    public function test_formatted_totals_include_currency(): void
    {
        $orders = $this->repository->findAll();
        $ordersList = $orders->getOrders();

        $this->assertEquals('GBP 315.00', $ordersList[0]->getTotalFormatted());
        $this->assertEquals('USD 425.00', $ordersList[1]->getTotalFormatted());
    }

    public function test_data_source_supports_streaming(): void
    {
        $dataSource = DataSourceFactory::createJsonLinesFile($this->ordersFilePath);
        
        $this->assertTrue($dataSource->supportsStreaming());
    }

    public function test_can_stream_orders_from_jsonl(): void
    {
        $dataSource = DataSourceFactory::createJsonLinesFile($this->ordersFilePath);
        $generator = $dataSource->stream();
        
        $this->assertInstanceOf(\Generator::class, $generator);
        
        $orders = [];
        foreach ($generator as $orderData) {
            $orders[] = $orderData;
        }
        
        $this->assertCount(2, $orders);
        $this->assertEquals('#1001', $orders[0]['order_number']);
        $this->assertEquals('#1002', $orders[1]['order_number']);
    }

    public function test_streaming_yields_complete_order_data(): void
    {
        $dataSource = DataSourceFactory::createJsonLinesFile($this->ordersFilePath);
        
        $firstOrder = null;
        foreach ($dataSource->stream() as $orderData) {
            $firstOrder = $orderData;
            break; // Only get first item
        }
        
        $this->assertNotNull($firstOrder);
        $this->assertArrayHasKey('order_number', $firstOrder);
        $this->assertArrayHasKey('title', $firstOrder);
        $this->assertArrayHasKey('currency', $firstOrder);
        $this->assertArrayHasKey('total', $firstOrder);
        $this->assertArrayHasKey('shippingAddress', $firstOrder);
        $this->assertArrayHasKey('line_items', $firstOrder);
    }

    public function test_streaming_processes_all_orders(): void
    {
        $dataSource = DataSourceFactory::createJsonLinesFile($this->ordersFilePath);
        
        $orderNumbers = [];
        foreach ($dataSource->stream() as $orderData) {
            $orderNumbers[] = $orderData['order_number'];
        }
        
        $this->assertEquals(['#1001', '#1002'], $orderNumbers);
    }
}
