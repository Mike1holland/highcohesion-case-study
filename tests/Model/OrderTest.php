<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\Tests\Model;

use Adhoc\HighCohesion\Model\Order;
use Adhoc\HighCohesion\Model\Address;
use Adhoc\HighCohesion\Model\LineItem;
use Adhoc\HighCohesion\Model\LineItemCollection;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    private Address $address;
    private LineItemCollection $lineItems;

    protected function setUp(): void
    {
        $this->address = new Address(
            address1: '1 Main Street',
            town: 'Test Town',
            city: 'London',
            countryCode: 'UK',
            zip: 'L1 1AA'
        );

        $lineItems = [
            new LineItem(
                sku: 'snow-sunglasses',
                title: 'Snow Sunglasses',
                quantity: 2,
                pricePence: 11000,
                totalPence: 22000
            ),
            new LineItem(
                sku: 'mountain-jacket',
                title: 'Mountain Jacket',
                quantity: 1,
                pricePence: 9500,
                totalPence: 9500
            )
        ];

        $this->lineItems = new LineItemCollection($lineItems);
    }

    public function testCanCreateOrder(): void
    {
        $order = new Order(
            orderNumber: '#1001',
            title: '#1001-R1',
            currency: 'GBP',
            totalPence: 31500,
            shippingAddress: $this->address,
            lineItems: $this->lineItems
        );

        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame('#1001', $order->orderNumber);
        $this->assertSame('#1001-R1', $order->title);
        $this->assertSame('GBP', $order->currency);
        $this->assertSame(31500, $order->totalPence);
    }

    public function testOrderImplementsTitleInterface(): void
    {
        $order = new Order(
            orderNumber: '#1001',
            title: '#1001-R1',
            currency: 'GBP',
            totalPence: 31500,
            shippingAddress: $this->address,
            lineItems: $this->lineItems
        );

        $this->assertSame('#1001-R1', $order->getTitle());
    }

    public function testOrderHasFormattedTotal(): void
    {
        $order = new Order(
            orderNumber: '#1001',
            title: '#1001-R1',
            currency: 'GBP',
            totalPence: 31500,
            shippingAddress: $this->address,
            lineItems: $this->lineItems
        );

        $this->assertSame('GBP 315.00', $order->getTotalFormatted());
    }

    public function testOrderHasShippingAddress(): void
    {
        $order = new Order(
            orderNumber: '#1001',
            title: '#1001-R1',
            currency: 'GBP',
            totalPence: 31500,
            shippingAddress: $this->address,
            lineItems: $this->lineItems
        );

        $this->assertInstanceOf(Address::class, $order->shippingAddress);
        $this->assertSame('1 Main Street', $order->shippingAddress->address1);
        $this->assertSame('London', $order->shippingAddress->city);
    }

    public function testOrderHasLineItems(): void
    {
        $order = new Order(
            orderNumber: '#1001',
            title: '#1001-R1',
            currency: 'GBP',
            totalPence: 31500,
            shippingAddress: $this->address,
            lineItems: $this->lineItems
        );

        $this->assertInstanceOf(LineItemCollection::class, $order->lineItems);
        $this->assertCount(2, $order->lineItems->getItems());
    }

    public function testOrderPropertiesAreReadonly(): void
    {
        $order = new Order(
            orderNumber: '#1001',
            title: '#1001-R1',
            currency: 'GBP',
            totalPence: 31500,
            shippingAddress: $this->address,
            lineItems: $this->lineItems
        );

        $reflection = new \ReflectionProperty(Order::class, 'shippingAddress');
        $this->assertTrue($reflection->isReadOnly());

        $reflection = new \ReflectionProperty(Order::class, 'lineItems');
        $this->assertTrue($reflection->isReadOnly());
    }
}
