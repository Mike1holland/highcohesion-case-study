<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\Model;

class Address
{
    public function __construct(
        public readonly string $address1,
        public readonly string $town,
        public readonly string $city,
        public readonly string $countryCode,
        public readonly string $zip
    ) {}
}