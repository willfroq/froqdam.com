<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Contract;

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Data\BlockElement;
use Pimcore\Model\DataObject\User;

interface AssetLibraryExtensionInterface
{
    public function portalAssetPath(Asset|null $asset): string;

    public function getClassName(object $object): ?string;

    /** @return array<array<BlockElement>>|null */
    public function getAssetLibraryColumnsForUser(User $user): ?array;

    public function getAvailableColumnLabel(string $columnId, User $user): ?string;

    public function isFilterAvailableForUser(User $user, string $filterID): bool;

    public function isAdmin(User $user): bool;
}
