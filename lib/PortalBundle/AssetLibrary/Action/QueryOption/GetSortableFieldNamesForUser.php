<?php

declare(strict_types=1);

namespace Froq\PortalBundle\AssetLibrary\Action\QueryOption;

use Pimcore\Model\DataObject\Data\BlockElement;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GetSortableFieldNamesForUser
{
    /** @return  array<int, string> */
    public function __invoke(#[CurrentUser] User $user): array
    {
        $settings = $user->getGroupAssetLibrarySettings();

        if (!($settings instanceof GroupAssetLibrarySettings)) {
            return [];
        }

        $sortableFilterNamesForUser = [];

        foreach ($settings->getAssetLibrarySortOptions() ?? [] as $setting) {
            if (!isset($setting['AssetLibrarySortLabel'])) {
                continue;
            }

            $property = $setting['AssetLibrarySortProperty'];

            if (!($property instanceof BlockElement)) {
                continue;
            }

            if (!$property->getData()) {
                continue;
            }

            $sortableFilterNamesForUser[] = $property->getData();
        }

        return $sortableFilterNamesForUser;
    }
}
