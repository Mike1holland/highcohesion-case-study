<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\DataSource;

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
}
