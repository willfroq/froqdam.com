<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\UserSettings\AssetLibrary;

use Froq\PortalBundle\PimcoreOptionsProvider\AssetLibColumnOptionsProvider;
use Pimcore\Model\DataObject\Data\BlockElement;
use Pimcore\Model\DataObject\User;

class ColumnConfigurationManager
{
    /**
     * @param string $columnKey
     * @param User|null $user
     *
     * @return string|null
     */
    public function getAvailableColumnLabel(string $columnKey, ?User $user = null): ?string
    {
        $label = $user ? $this->getConfiguredColumnLabelForUser($columnKey, $user) : null;

        return $label ?? $this->getDefaultAssetLibraryColumnLabel($columnKey);
    }

    /**
     * @param string $columnKey
     * @param User|null $user
     *
     * @return string|null
     */
    public function getConfiguredColumnLabelForUser(string $columnKey, ?User $user = null): ?string
    {
        $groupAssetLibrarySettings = $user?->getGroupAssetLibrarySettings() ?? [];
        if (!$groupAssetLibrarySettings) {
            return null;
        }

        foreach ($groupAssetLibrarySettings->getAssetLibraryColumnsOptions() ?? [] as $option) {
            /** @var BlockElement $element */
            foreach ($option as $element) {
                if (($element->getName() !== 'AssetLibraryColumnProperty') || ($element->getData() !== $columnKey)) {
                    continue;
                }

                if (!empty($option['AssetLibraryColumnLabel']) && $option['AssetLibraryColumnLabel']->getData()) {
                    return $option['AssetLibraryColumnLabel']->getData();
                }
            }
        }

        return null;
    }

    /**
     * @param User $user
     *
     * @return array<int<0, max>, mixed>
     */
    public static function getColumnKeysForUser(User $user): array
    {
        $groupAssetLibrarySettings = $user->getGroupAssetLibrarySettings();
        if (!$groupAssetLibrarySettings) {
            return [];
        }

        $keys = [];
        foreach ($groupAssetLibrarySettings->getAssetLibraryColumnsOptions() ?? [] as $option) {
            /** @var BlockElement $element */
            foreach ($option as $element) {
                if (($element->getName() !== 'AssetLibraryColumnProperty') || !$element->getData()) {
                    continue;
                }

                $keys[] = $element->getData();
            }
        }

        return $keys;
    }

    /**
     * @param string $columnKey
     *
     * @return string|null
     */
    public static function getDefaultAssetLibraryColumnLabel(string $columnKey): ?string
    {
        $options = AssetLibColumnOptionsProvider::getKeyValues();
        foreach ($options as $option) {
            if ($option['value'] === $columnKey) {
                return $option['key'];
            }
        }

        return null;
    }

    /**
     * @param User $user
     *
     * @return array<array<BlockElement>>|null
     */
    public function getAssetLibraryColumnsOptions(User $user): ?array
    {
        return $user->getGroupAssetLibrarySettings()?->getAssetLibraryColumnsOptions();
    }
}
