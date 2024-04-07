<?php

declare(strict_types=1);

namespace Froq\PortalBundle\DTO;

class AggregationChoiceDto
{
    private string $key;
    private int $docCount;

    /**
     * @param string $key
     * @param int $docCount
     */
    public function __construct(string $key, int $docCount)
    {
        $this->key = $key;
        $this->docCount = $docCount;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return int
     */
    public function getDocCount(): int
    {
        return $this->docCount;
    }
}
