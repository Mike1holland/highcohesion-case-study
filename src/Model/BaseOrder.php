<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\Model;

/**
 * Base class for all order types
 * Contains shared fields: orderNumber, title, currency, total
 */
abstract class BaseOrder implements TitleInterface
{
    public function __construct(
        public readonly string $orderNumber,
        public readonly string $title,
        public readonly string $currency,
        public readonly int $total
    ) {}
    
    /**
     * Get the title (required by TitleInterface)
     */
    public function getTitle(): string
    {
        return $this->title;
    }
    
    /**
     * Get formatted total with currency
     */
    public function getTotalFormatted(): string
    {
        return $this->currency . ' ' . number_format($this->total / 100, 2);
    }
}
