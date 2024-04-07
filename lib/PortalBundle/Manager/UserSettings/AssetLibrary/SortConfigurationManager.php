<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\UserSettings\AssetLibrary;

use Froq\PortalBundle\PimcoreOptionsProvider\AssetLibSortOptionsProvider;
use Pimcore\Model\DataObject\Data\BlockElement;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Core\User\UserInterface;

class SortConfigurationManager
{
    /**
     * @param string $fieldId
     * @param User|null $user
     *
     * @return string|null
     */
    public function getAvailableSortLabel(string $fieldId, ?User $user = null): ?string
    {
        $label = $user ? $this->getConfiguredSortLabelForUser($fieldId, $user) : null;

        return $label ?? $this->getDefaultAssetLibrarySortLabel($fieldId);
    }

    /**
     * @param UserInterface $user
     *
     * @return array<string|int, mixed>
     */
    public static function getSortKeysForUser(UserInterface $user): array
    {
        /** @var GroupAssetLibrarySettings $groupAssetLibrarySettings */
        $groupAssetLibrarySettings = $user->getGroupAssetLibrarySettings(); /** @phpstan-ignore-line */
        if (!$groupAssetLibrarySettings) {
            return [];
        }

        $keys = [];
        foreach ($groupAssetLibrarySettings->getAssetLibrarySortOptions() ?? [] as $option) {
            /** @var BlockElement $element */
            foreach ($option as $element) {
                if (($element->getName() !== 'AssetLibrarySortProperty') || !$element->getData()) {
                    continue;
                }

                $keys[] = $element->getData();
            }
        }

        return array_unique($keys);
    }

    /**
     * @param string $filterId
     * @param User|null $user
     *
     * @return string|null
     */
    public function getConfiguredSortLabelForUser(string $filterId, ?User $user = null): ?string
    {
        $groupAssetLibrarySettings = $user?->getGroupAssetLibrarySettings();
        if (!$groupAssetLibrarySettings) {
            return null;
        }

        $options = $groupAssetLibrarySettings->getAssetLibrarySortOptions() ?? [];
        foreach ($options as $option) {
            /** @var BlockElement $element */
            foreach ($option as $element) {
                if (($element->getName() !== 'AssetLibrarySortProperty') || ($element->getData() !== $filterId)) {
                    continue;
                }

                if (!empty($option['AssetLibrarySortLabel']) && $option['AssetLibrarySortLabel']->getData()) {
                    return $option['AssetLibrarySortLabel']->getData();
                }
            }
        }

        return null;
    }

    /**
     * @param string $filterId
     *
     * @return string|null
     */
    public function getDefaultAssetLibrarySortLabel(string $filterId): ?string
    {
        $options = AssetLibSortOptionsProvider::getKeyValues();
        foreach ($options as $option) {
            if ($option['value'] === $filterId) {
                return $option['key'];
            }
        }

        return null;
    }
}
