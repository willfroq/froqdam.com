<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Contract;

use Pimcore\Model\DataObject\AssetResource;

interface AssetResourceRepositoryInterface
{
    public function getAssetResourceByIdWithChecks(int $id): ?AssetResource;

    public function fetchDeepestChildId(int $parentId): int;
}
