<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection;

use Webmozart\Assert\Assert;

final class AssetInfoSectionMetadataCollection
{
    public function __construct(
        /** @var AssetInfoSectionMetadataItem[] $items */
        public readonly array $items,
    ) {
        Assert::isArray($this->items, 'Expected "items" to be a array, got %s');
    }
}
