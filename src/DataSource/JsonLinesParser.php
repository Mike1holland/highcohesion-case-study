<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\DataSource;

use Generator;
use JsonException;

class JsonLinesParser implements StreamingParserInterface
{
    /**
     * Parse JSONL (JSON Lines) format
     * 
     * @param string $rawData
     * @return array Array of decoded JSON objects
     * @throws JsonException
     */
    public function parse(string $rawData): array
    {
        $lines = explode("\n", trim($rawData));
        $results = [];
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // Skip empty lines
            if ($trimmedLine === '') {
                continue;
            }
            
            $results[] = json_decode($trimmedLine, true, 512, JSON_THROW_ON_ERROR);
        }
        
        return $results;
    }

    /**
     * Parse JSONL in streaming mode - yields one object at a time
     * More memory efficient for large files
     * 
     * @param string $rawData
     * @return Generator
     * @throws JsonException
     */
    public function parseStream(string $rawData): Generator
    {
        $lines = explode("\n", trim($rawData));
        
        foreach ($lines as $index => $line) {
            $trimmedLine = trim($line);
            
            // Skip empty lines
            if ($trimmedLine === '') {
                continue;
            }
            
            yield $index => json_decode($trimmedLine, true, 512, JSON_THROW_ON_ERROR);
        }
    }
}
