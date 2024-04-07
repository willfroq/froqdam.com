<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\UserSettings\AssetLibrary;

use Froq\PortalBundle\PimcoreOptionsProvider\AssetLibFilterOptionsProvider;
use Pimcore\Model\DataObject\Data\BlockElement;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Core\User\UserInterface;

class FilterConfigurationManager
{
    /**
     * @param string $filterId
     * @param User|null $user
     *
     * @return string|null
     */
    public function getAvailableFilterLabel(string $filterId, ?User $user = null): ?string
    {
        $label = $user ? $this->getConfiguredFilterLabelForUser($filterId, $user) : null;

        return $label ?? $this->getDefaultAssetLibraryFilterLabel($filterId);
    }

    /**
     * @param UserInterface $user
     *
     * @return array<int, mixed>|null
     */
    public function getConfiguredFilterIdsForUser(UserInterface $user): ?array
    {
        $groupAssetLibrarySettings = $user->getGroupAssetLibrarySettings(); /** @phpstan-ignore-line */
        if (!$groupAssetLibrarySettings) {
            return null;
        }

        $userFields = [];
        foreach ($groupAssetLibrarySettings->getAssetLibraryFilterOptions() as $option) {
            /** @var BlockElement $element */
            foreach ($option as $element) {
                if ($element->getName() === 'AssetLibraryFilterProperty') {
                    $userFields[] = $element->getData();
                    break;
                }
            }
        }

        return array_unique($userFields);
    }

    /**
     * @param string $filterId
     * @param User|null $user
     *
     * @return string|null
     */
    public function getConfiguredFilterLabelForUser(string $filterId, ?User $user = null): ?string
    {
        $groupAssetLibrarySettings = $user?->getGroupAssetLibrarySettings();
        if (!$groupAssetLibrarySettings) {
            return null;
        }

        $options = $groupAssetLibrarySettings->getAssetLibraryFilterOptions() ?? [];
        foreach ($options as $option) {
            /** @var BlockElement $element */
            foreach ($option as $element) {
                if (($element->getName() !== 'AssetLibraryFilterProperty') || ($element->getData() !== $filterId)) {
                    continue;
                }

                if (!empty($option['AssetLibraryFilterLabel']) && $option['AssetLibraryFilterLabel']->getData()) {
                    return $option['AssetLibraryFilterLabel']->getData();
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
    public function getDefaultAssetLibraryFilterLabel(string $filterId): ?string
    {
        $options = AssetLibFilterOptionsProvider::getKeyValues();
        foreach ($options as $option) {
            if ($option['value'] === $filterId) {
                return $option['key'];
            }
        }

        return null;
    }
}
