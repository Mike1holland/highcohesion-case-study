<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\DataSource;

use Generator;

class JsonFileDataSource implements DataSourceInterface
{
    public function __construct(
        private readonly FileDataFetcher $fetcher,
        private readonly ParserInterface $parser
    ) {}

    /**
     * Get all data using pipeline
     * 
     * @return array
     * @throws \JsonException
     */
    public function getAll(): array
    {
        $rawData = $this->fetcher->fetch();
        return $this->parser->parse($rawData);
    }
    
    /**
     * Get a single item by key
     * 
     * @param string $key
     * @return array|null
     * @throws \JsonException
     */
    public function getOne(string $key): ?array
    {
        $data = $this->getAll();
        return $data[$key] ?? null;
    }

    /**
     * Stream JSON data line by line for large files
     * Useful for processing large arrays without loading everything into memory
     * 
     * @return Generator
     */
    public function stream(): Generator
    {
        $rawData = $this->fetcher->fetch();
        $data = $this->parser->parse($rawData);

        foreach ($data as $key => $item) {
            yield $key => $item;
        }
    }
}
