<?php

declare(strict_types=1);

namespace Froq\PortalBundle\AssetLibrary\Action\QueryOption;

use Froq\PortalBundle\Opensearch\Action\GetYamlConfigFileProperties;
use Froq\PortalBundle\Opensearch\ValueObject\SidebarFilter;
use Pimcore\Model\DataObject\Data\BlockElement;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GetInitialSidebarFilters
{
    public function __construct(private readonly GetYamlConfigFileProperties $getYamlConfigFileProperties)
    {
    }

    /**
     * @throws \Exception
     *
     * @return array<int, SidebarFilter>
     */
    public function __invoke(string $indexName, #[CurrentUser] User $user): array
    {
        $settings = $user->getGroupAssetLibrarySettings();

        if (!($settings instanceof GroupAssetLibrarySettings)) {
            return [];
        }

        $sidebarFilters = [];

        foreach ($settings->getAssetLibraryFilterOptions() ?? [] as $setting) {
            if (!isset($setting['AssetLibraryFilterLabel'])) {
                continue;
            }

            $labelElement = $setting['AssetLibraryFilterLabel'];

            if (!($labelElement instanceof BlockElement)) {
                continue;
            }

            $property = $setting['AssetLibraryFilterProperty'];

            if (!($property instanceof BlockElement)) {
                continue;
            }

            $filterName = $property->getData();

            $mappedFilters = ($this->getYamlConfigFileProperties)($indexName);

            if (!isset($mappedFilters[$filterName])) {
                continue;
            }

            if (!isset($mappedFilters[$filterName]['type'])) {
                continue;
            }

            $type = $mappedFilters[$filterName]['type'];

            $label = (string) $labelElement->getData();

            $label = empty($label) ? ucfirst((string) str_replace('_', ' ', $filterName)) : $label;

            $sidebarFilters[] = new SidebarFilter(
                filterName: (string) $filterName,
                label: $label,
                type: (string) $type,
                aggregation: null,
                dateRangeFilter: null,
                numberRangeFilter: null,
                inputFilter: null,
                shouldExpand: false
            );
        }

        return $sidebarFilters;
    }
}
