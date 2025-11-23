<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\DataSource;

class JsonParser implements ParserInterface
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
}
