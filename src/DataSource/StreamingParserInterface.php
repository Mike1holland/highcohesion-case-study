<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\DataSource;

use Generator;

/**
 * Interface for parsers that support streaming
 */
interface StreamingParserInterface extends ParserInterface
{
    /**
     * Parse data in streaming mode, yielding one item at a time
     * 
     * @param string $rawData
     * @return Generator
     * @throws \JsonException
     */
    public function parseStream(string $rawData): Generator;
}
