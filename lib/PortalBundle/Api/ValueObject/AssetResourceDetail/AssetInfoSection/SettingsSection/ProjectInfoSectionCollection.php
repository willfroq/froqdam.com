<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\SettingsSection;

use Webmozart\Assert\Assert;

final class ProjectInfoSectionCollection
{
    public function __construct(
        /** @var array<int, ProjectInfoSectionItem> */
        public array $items = [],
    ) {
        Assert::isArray($this->items, 'Expected "items" to be a array, got %s');
    }

    public function add(ProjectInfoSectionItem $item): void
    {
        $this->items[] = $item;
    }

    public function getItemByName(string $name): ?ProjectInfoSectionItem
    {
        foreach ($this->items as $productInfoSectionItem) {
            if ($productInfoSectionItem->name === $name) {
                return $productInfoSectionItem;
            }
        }

        return null;
    }
}
