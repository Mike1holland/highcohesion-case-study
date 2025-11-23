<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\Model;

class LineItemCollection
{
    /**
     * @var LineItem[]
     */
    private array $items = [];

    /**
     * @param LineItem[] $items
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * Add a line item to the collection
     */
    public function add(LineItem $item): void
    {
        $this->items[] = $item;
    }

    /**
     * Get all line items in the collection
     * 
     * @return LineItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Get the count of line items
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Check if collection is empty
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * Calculate total value of all line items
     */
    public function getTotalValue(): int
    {
        return array_reduce(
            $this->items,
            fn(int $carry, LineItem $item) => $carry + $item->total,
            0
        );
    }
}
