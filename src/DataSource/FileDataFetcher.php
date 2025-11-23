<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\DataSource;

use RuntimeException;

class FileDataFetcher implements DataFetcherInterface
{
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
