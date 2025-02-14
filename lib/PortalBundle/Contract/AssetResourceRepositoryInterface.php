<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Contract;

use Pimcore\Model\DataObject\AssetResource;

interface AssetResourceRepositoryInterface
{
    public function getAssetResourceByIdWithChecks(int $id): ?AssetResource;

    public function fetchDeepestChildId(int $parentId): int;

    /**
     * @return array<int, int>
     */
    public function fetchParentIds(int $lastId, int $limit): array;

    public function countParentIds(): int;

    public function hasLinkedTabItem(?AssetResource $assetResource): bool;

    public function hasRelatedTabItem(?AssetResource $assetResource): bool;
}
