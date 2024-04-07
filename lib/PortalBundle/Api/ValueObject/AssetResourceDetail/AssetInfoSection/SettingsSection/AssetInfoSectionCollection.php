<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\SettingsSection;

use Webmozart\Assert\Assert;

final class AssetInfoSectionCollection
{
    public function __construct(
        /** @var array<int, AssetInfoSectionItem> */
        public array $items = [],
    ) {
        Assert::isArray($this->items, 'Expected "items" to be a array, got %s');
    }

    public function add(AssetInfoSectionItem $item): void
    {
        $this->items[] = $item;
    }

    public function getItemByName(string $name): ?AssetInfoSectionItem
    {
        foreach ($this->items as $assetInfoSectionItem) {
            if ($assetInfoSectionItem->name === $name) {
                return $assetInfoSectionItem;
            }
        }

        return null;
    }
}
