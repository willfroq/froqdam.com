<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\DataTransferObject;

use Webmozart\Assert\Assert;

final class AssetResourceExportCollection
{
    public function __construct(
        public readonly int $organizationId,
        public readonly int $offset,
        public readonly int $limit,
        public readonly int $totalCount,
        /** @var array<int, AssetResourceExportItem> */
        public readonly array $parentAssetResourceExportItems,
    ) {
        Assert::numeric($this->organizationId, 'Expected "organizationId" to be a numeric, got %s');
        Assert::numeric($this->offset, 'Expected "totalPages" to be a numeric, got %s');
        Assert::numeric($this->limit, 'Expected "currentPage" to be a numeric, got %s');
        Assert::numeric($this->totalCount, 'Expected "totalCount" to be a numeric, got %s');
        Assert::isArray($this->parentAssetResourceExportItems, 'Expected "parentAssetResourceExportItems" to be an array, got %s');
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'organizationId' => $this->organizationId,
            'offset' => $this->offset,
            'limit' => $this->limit,
            'totalCount' => $this->totalCount,
            'parentAssetResourceExportItems' => $this->parentAssetResourceExportItems,
        ];
    }
}
