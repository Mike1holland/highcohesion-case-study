<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\Model;

class LineItem
{
    public function __construct(
        public readonly string $sku,
        public readonly string $title,
        public readonly int $quantity,
        public readonly int $price,
        public readonly int $total
    ) {}
}
