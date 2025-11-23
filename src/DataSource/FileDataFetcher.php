<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\DataSource;

use RuntimeException;

/**
 * Fetch data from a file
 */
class FileDataFetcher implements DataFetcherInterface
{
    /**
     * @param string $filePath
     * @throws RuntimeException
     */
    public function __construct(
        private readonly string $filePath
    ) {
        if (!file_exists($this->filePath)) {
            throw new RuntimeException("File not found: {$this->filePath}");
        }
    }

    /**
     * Fetch raw file contents
     * 
     * @return string
     */
    public function fetch(): string
    {
        return file_get_contents($this->filePath);
    }
}
