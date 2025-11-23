<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\DataSource;

use Generator;

class JsonParser implements StreamingParserInterface
{
    /**
     * Decode JSON string into an array
     * 
     * @param string $rawData
     * @return array
     * @throws \JsonException
     */
    public function parse(string $rawData): array
    {
        return json_decode($rawData, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Parse JSON in streaming mode - yields array items one at a time
     * Only works if the JSON root is an array
     * 
     * @param string $rawData
     * @return Generator
     * @throws \JsonException
     */
    public function parseStream(string $rawData): Generator
    {
        $data = json_decode($rawData, true, 512, JSON_THROW_ON_ERROR);

        if (is_array($data) && array_is_list($data)) {
            foreach ($data as $index => $item) {
                yield $index => $item;
            }
        } else {
            yield 0 => $data;
        }
    }
}
