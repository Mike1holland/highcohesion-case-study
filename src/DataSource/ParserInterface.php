<?php

declare(strict_types=1);

namespace Adhoc\HighCohesion\DataSource;

interface ParserInterface
{
    /**
     * Decode raw data into an array
     * 
     * @param string $rawData
     * @return array
     */
    public function parse(string $rawData): array;
}
