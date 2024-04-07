<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\ValueObject\AssetResourceDetail;

use Webmozart\Assert\Assert;

final class AssetResourceDetail
{
    public function __construct(
        public readonly int $id,
        public readonly string $portalAssetDownloadPath,
        public readonly AssetItem $assetItem,
        public readonly ?ProductCollection $products,
        public readonly ?ProjectCollection $projects,
        public readonly SettingsItem $settings,
    ) {
        Assert::numeric($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::string($this->portalAssetDownloadPath, 'Expected "portalAssetDownloadPath" to be a string, got %s');
        Assert::isInstanceOf($this->assetItem, AssetItem::class, 'Expected "assetItem" to be instance of AssetItem, got %s');
        Assert::nullOrIsInstanceOf($this->products, ProductCollection::class, 'Expected "products" to be instance of ProductCollection, got %s');
        Assert::nullOrIsInstanceOf($this->projects, ProjectCollection::class, 'Expected "projects" to be instance of ProjectCollection, got %s');
        Assert::isInstanceOf($this->settings, SettingsItem::class, 'Expected "settings" to be instance of GroupAssetLibrarySettings, got %s');
    }
}
