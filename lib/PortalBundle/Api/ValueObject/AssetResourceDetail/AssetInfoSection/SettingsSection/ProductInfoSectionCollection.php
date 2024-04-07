<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\SettingsSection;

use Webmozart\Assert\Assert;

final class ProductInfoSectionCollection
{
    public function __construct(
        /** @var array<int, ProductInfoSectionItem> */
        public array $items = [],
    ) {
        Assert::isArray($this->items, 'Expected "items" to be a array, got %s');
    }

    public function add(ProductInfoSectionItem $item): void
    {
        $this->items[] = $item;
    }

    public function getItemByName(string $name): ?ProductInfoSectionItem
    {
        foreach ($this->items as $productInfoSectionItem) {
            if ($productInfoSectionItem->name === $name) {
                return $productInfoSectionItem;
            }
        }

        return null;
    }
}
