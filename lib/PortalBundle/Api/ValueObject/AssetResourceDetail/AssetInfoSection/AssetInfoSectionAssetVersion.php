<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection;

use Webmozart\Assert\Assert;

final class AssetInfoSectionAssetVersion
{
    public function __construct(
        public readonly string $name,
        public readonly bool $isEnabled,
        public readonly string $label,
        public readonly string $tableRowLabelAssetTypeName,
        public readonly string $assetResourceVersion,
    ) {
        Assert::string($this->name, 'Expected "name" to be a string, got %s');
        Assert::boolean($this->isEnabled, 'Expected "isEnabled" to be a bool, got %s');
        Assert::string($this->label, 'Expected "label" to be a string, got %s');
        Assert::string($this->tableRowLabelAssetTypeName, 'Expected "tableRowLabelAssetTypeName" to be a string, got %s');
        Assert::string($this->assetResourceVersion, 'Expected "assetResourceVersion" to be a string, got %s');
    }
}
