<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\Model;


abstract class BaseOrder implements TitleInterface
{
    public function __construct(
        public readonly string $orderNumber,
        public readonly string $title,
        public readonly string $currency,
        public readonly int $totalPence
    ) {}
    
    public function getTitle(): string
    {
        return $this->title;
    }
    
    public function getTotalFormatted(): string
    {
        return $this->currency . ' ' . number_format($this->totalPence / 100, 2);
    }
}
