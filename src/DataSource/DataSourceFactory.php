<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\DataSource;

/**
 * Factory for creating pre-configured DataSource instances
 */
class DataSourceFactory
{
    /**
     * Create a JSON file data source
     * 
     * @param string $filePath Path to the JSON file
     * @return DataSourceInterface
     */
    public static function createJsonFile(string $filePath): DataSourceInterface
    {
        return new JsonFileDataSource(
            new FileDataFetcher($filePath),
            new JsonParser()
        );
    }

    /**
     * Create a JSONL (JSON Lines) file data source
     * 
     * @param string $filePath Path to the JSONL file
     * @return DataSourceInterface
     */
    public static function createJsonLinesFile(string $filePath): DataSourceInterface
    {
        return new JsonFileDataSource(
            new FileDataFetcher($filePath),
            new JsonLinesParser()
        );
    }
}
