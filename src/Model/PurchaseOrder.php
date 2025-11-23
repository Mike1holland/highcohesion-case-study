<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\Model;

class PurchaseOrder extends BaseOrder
{
    public function __construct(
        string $orderNumber,
        string $title,
        string $currency,
        int $total,
        public readonly string $vendorId,
        public readonly \DateTimeImmutable $purchaseDate,
        public readonly string $approvalStatus = 'pending'
    ) {
        parent::__construct($orderNumber, $title, $currency, $total);
    }
}
