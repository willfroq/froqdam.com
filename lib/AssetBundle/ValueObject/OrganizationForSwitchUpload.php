<?php

declare(strict_types=1);

namespace Froq\AssetBundle\ValueObject;

use Webmozart\Assert\Assert;

final class OrganizationForSwitchUpload
{
    public function __construct(
        public readonly int $id,
        public readonly string $fullPath,
        public readonly string $code,
        public readonly string $name,
        public readonly string $rootAssetResourceFolder,
        public readonly string $assetFolder,
        public readonly string $orgAssetFolder,
        public readonly string $orgProjectFolder,
        public readonly string $orgProductFolder,
        public readonly string $orgTagFolder,
        public readonly string $orgCategoriesFolder,
        public readonly string $orgCategoriesBrandsFolder,
        public readonly string $orgCategoriesCampaignsFolder,
        public readonly string $orgCategoriesMarketsFolder,
        public readonly string $orgCategoriesSegmentsFolder,
        public readonly string $orgCategoriesPlatformsFolder,
        public readonly string $assetTypePathFromSwitch,
        public readonly string $orgObjectAssetsFolder,
    ) {
        Assert::numeric($this->id, 'Expected "id" to be a string, got %s');
        Assert::string($this->fullPath, 'Expected "fullPath" to be a string, got %s');
        Assert::string($this->code, 'Expected "code" to be a string, got %s');
        Assert::string($this->name, 'Expected "name" to be a string, got %s');
        Assert::string($this->rootAssetResourceFolder, 'Expected "rootAssetResourceFolder" to be a string, got %s');
        Assert::string($this->assetFolder, 'Expected "assetFolder" to be a string, got %s');
        Assert::string($this->orgAssetFolder, 'Expected "orgAssetFolder" to be a string, got %s');
        Assert::string($this->orgProjectFolder, 'Expected "orgProjectFolder" to be a string, got %s');
        Assert::string($this->orgProductFolder, 'Expected "orgProductFolder" to be a string, got %s');
        Assert::string($this->orgTagFolder, 'Expected "orgTagFolder" to be a string, got %s');
        Assert::string($this->orgCategoriesFolder, 'Expected "orgCategoriesFolder" to be a string, got %s');
        Assert::string($this->orgCategoriesBrandsFolder, 'Expected "orgCategoriesBrandsFolder" to be a string, got %s');
        Assert::string($this->orgCategoriesCampaignsFolder, 'Expected "orgCategoriesCampaignsFolder" to be a string, got %s');
        Assert::string($this->orgCategoriesMarketsFolder, 'Expected "orgCategoriesMarketsFolder" to be a string, got %s');
        Assert::string($this->orgCategoriesSegmentsFolder, 'Expected "orgCategoriesSegmentsFolder" to be a string, got %s');
        Assert::string($this->orgCategoriesPlatformsFolder, 'Expected "orgCategoriesPlatformsFolder" to be a string, got %s');
        Assert::string($this->assetTypePathFromSwitch, 'Expected "assetTypePathFromSwitch" to be a string, got %s');
        Assert::string($this->orgObjectAssetsFolder, 'Expected "orgObjectAssetsFolder" to be a string, got %s');
    }
}
