<?php

declare(strict_types=1);

namespace Froq\PortalBundle\AssetLibrary\Action\QueryOption;

use Froq\PortalBundle\Opensearch\ValueObject\SortOption;
use Pimcore\Model\DataObject\Data\BlockElement;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GetSortOptionsForUser
{
    /**
     * @param User $user
     * @param array<int, string> $sortableNames
     *
     * @return array<int, SortOption>
     */
    public function __invoke(array $sortableNames, #[CurrentUser] User $user): array
    {
        $settings = $user->getGroupAssetLibrarySettings();

        if (!($settings instanceof GroupAssetLibrarySettings)) {
            return [];
        }

        $sortOptions = [];

        foreach ($settings->getAssetLibrarySortOptions() ?? [] as $setting) {
            if (!isset($setting['AssetLibrarySortLabel'])) {
                continue;
            }

            $label = $setting['AssetLibrarySortLabel'];

            if (!($label instanceof BlockElement)) {
                continue;
            }

            $property = $setting['AssetLibrarySortProperty'];

            if (!($property instanceof BlockElement)) {
                continue;
            }

            $filterName = $property->getData();

            if (!$filterName) {
                continue;
            }

            if (!in_array(needle: $filterName, haystack: $sortableNames)) {
                continue;
            }

            $sortOptions[] = new SortOption(label: (string) $label->getData(), filterName: (string) $filterName, sortDirection: 'asc');
            $sortOptions[] = new SortOption(label: (string) $label->getData(), filterName: (string) $filterName, sortDirection: 'desc');
        }

        return $sortOptions;
    }
}
