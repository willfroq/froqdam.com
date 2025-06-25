<?php

declare(strict_types=1);

namespace Froq\PortalBundle\AssetLibrary\Action\QueryOption;

use Froq\PortalBundle\Opensearch\ValueObject\Column;
use Pimcore\Model\DataObject\Data\BlockElement;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GetColumnForUser
{
    /** @return  array<int, Column>
     * @throws \Exception
     */
    public function __invoke(#[CurrentUser] User $user, string $sortDirection, string $sortBy): array
    {
        $settings = $user->getGroupAssetLibrarySettings();

        if (!($settings instanceof GroupAssetLibrarySettings)) {
            return [];
        }

        $columns = [];

        foreach ($settings->getAssetLibraryColumnsOptions() ?? [] as $setting) {
            if (!isset($setting['AssetLibraryColumnProperty'])) {
                continue;
            }

            $labelElement = $setting['AssetLibraryColumnLabel'];

            if (!($labelElement instanceof BlockElement)) {
                continue;
            }

            $property = $setting['AssetLibraryColumnProperty'];

            if (!($property instanceof BlockElement)) {
                continue;
            }

            $filterName = (string) $property->getData();

            $label = (string) $labelElement->getData();

            $label = empty($label) ? ucfirst((string) str_replace('_', ' ', $filterName)) : $label;

            $columns[] = new Column(
                label: $label,
                filterName: $filterName,
                sortDirection: $filterName === $sortBy ? $sortDirection : ''
            );
        }

        return $columns;
    }
}
