<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection;

use Webmozart\Assert\Assert;

final class AssetInfoSectionMetadataItem
{
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        public readonly bool $isAvailableForUser,
        public readonly string $linkValue,
        public readonly string $value,
    ) {
        Assert::string($this->key, 'Expected "key" to be a string, got %s');
        Assert::string($this->label, 'Expected "label" to be a string, got %s');
        Assert::boolean($this->isAvailableForUser, 'Expected "isAvailableForUser" to be a bool, got %s');
        Assert::string($this->linkValue, 'Expected "linkValue" to be a string, got %s');
        Assert::string($this->value, 'Expected "value" to be a string, got %s');
    }
}
