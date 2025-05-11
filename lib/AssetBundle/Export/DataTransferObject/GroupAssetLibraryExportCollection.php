<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\DataTransferObject;

use Webmozart\Assert\Assert;

final class GroupAssetLibraryExportCollection
{
    public function __construct(
        public readonly int $offset,
        public readonly int $limit,
        public readonly int $totalCount,
        /** @var array<int, GroupAssetLibrarySettingsExportItem> */
        public readonly array $groupAssetLibrarySettingsExportItems,
    ) {
        Assert::numeric($this->offset, 'Expected "totalPages" to be a numeric, got %s');
        Assert::numeric($this->limit, 'Expected "currentPage" to be a numeric, got %s');
        Assert::numeric($this->totalCount, 'Expected "totalCount" to be a numeric, got %s');
        Assert::isArray($this->groupAssetLibrarySettingsExportItems, 'Expected "groupAssetLibrarySettingsExportItems" to be an array, got %s');
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'offset' => $this->offset,
            'limit' => $this->limit,
            'totalCount' => $this->totalCount,
            'groupAssetLibrarySettingsExportItems' => $this->groupAssetLibrarySettingsExportItems,
        ];
    }
}
