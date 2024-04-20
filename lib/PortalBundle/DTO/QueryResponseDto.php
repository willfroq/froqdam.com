<?php

declare(strict_types=1);

namespace Froq\PortalBundle\DTO;

use Pimcore\Model\DataObject\AssetResource;

class QueryResponseDto
{
    /**
     * @var array<int, mixed>
     */
    private array $objects = [];

    /**
     * @var array<string|int, mixed>
     */
    private array $aggregationDTOs = [];
    private int $totalCount = 0;

    /**
     * @return array<int, AssetResource>
     */
    public function getObjects(): array
    {
        return $this->objects;
    }

    /**
     * @param array<int, AssetResource> $objects
     */
    public function setObjects(array $objects): void
    {
        $this->objects = $objects;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function getAggregationDTOs(): array
    {
        return $this->aggregationDTOs;
    }

    /**
     * @param array<string|int, mixed> $aggregationDTOs
     */
    public function setAggregationDTOs(array $aggregationDTOs): void
    {
        $this->aggregationDTOs = $aggregationDTOs;
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @param int $totalCount
     */
    public function setTotalCount(int $totalCount): void
    {
        $this->totalCount = $totalCount;
    }
}
