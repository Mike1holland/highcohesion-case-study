<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\DataSource;

use Generator;

interface DataSourceInterface
{
    /**
     * Get all data from the source
     * 
     * @return array
     */
    public function getAll(): array;
    
    /**
     * Get a single item by key/ID
     * 
     * @param string $key
     * @return array|null
     */
    public function getOne(string $key): ?array;

    /**
     * Stream data one item at a time
     * 
     * @return Generator
     */
    public function stream(): Generator;

    /**
     * Check if this data source supports efficient streaming
     * 
     * @return bool
     */
    public function supportsStreaming(): bool;
}
