<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\DataSource;

interface DataFetcherInterface
{
    /**
     * Fetch raw data as string
     * 
     * @return string
     */
    public function fetch(): string;
}
