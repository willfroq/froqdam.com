<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Contract;

use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Project;

interface PortalDetailExtensionInterface
{
    public function portalAssetResourceVersion(AssetResource $assetResource): ?string;

    public function portalAssetResourceProductSku(AssetResource $assetResource): ?string;

    public function portalAssetResourceProjectName(AssetResource $assetResource): ?string;

    public function portalAssetResourceFileDateAdded(AssetResource $assetResource): ?string;

    public function portalAssetResourceFileDateCreated(AssetResource $assetResource): ?string;

    public function portalAssetResourceFileDateModified(AssetResource $assetResource): ?string;

    /** @return array<int|string, array<int|string, string>>|null */
    public function portalProductCategoryHierarchies(Product $product): ?array;

    public function portalProjectCategoryManagers(Project $project): ?string;

    public function portalPluralizeLabel(string $label, mixed $count): ?string;

    /** @return AbstractObject[]|null */
    public function portalAssetResourceProducts(AssetResource $assetResource): ?array;

    /** @return AbstractObject[]|null */
    public function portalAssetResourceProjects(AssetResource $assetResource): ?array;

    public function portalAssetResourceProductEan(AssetResource $assetResource): ?string;

    public function portalAssetResourceProductName(AssetResource $assetResource): ?string;

    public function portalAssetResourceFroqProjectNumber(AssetResource $assetResource): ?string;

    public function portalAssetResourcePimProjectNumber(AssetResource $assetResource): ?string;
}
