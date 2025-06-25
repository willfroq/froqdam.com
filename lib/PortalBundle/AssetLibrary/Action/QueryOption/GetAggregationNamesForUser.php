<?php

declare(strict_types=1);

namespace Froq\PortalBundle\AssetLibrary\Action\QueryOption;

use Froq\PortalBundle\Opensearch\Action\GetYamlConfigFileProperties;
use Pimcore\Model\DataObject\Data\BlockElement;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\User;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GetAggregationNamesForUser
{
    public function __construct(private readonly GetYamlConfigFileProperties $getYamlConfigFileProperties)
    {
    }

    /**
     * @return  array<int, string>
     *
     * @throws InvalidArgumentException
     * @throws \Exception*/
    public function __invoke(string $indexName, #[CurrentUser] User $user): array
    {
        $settings = $user->getGroupAssetLibrarySettings();

        if (!($settings instanceof GroupAssetLibrarySettings)) {
            return [];
        }

        $mappedFilters = ($this->getYamlConfigFileProperties)($indexName);

        $aggregationNames = [];

        foreach ($settings->getAssetLibraryFilterOptions() ?? [] as $setting) {
            if (!isset($setting['AssetLibraryFilterLabel'])) {
                continue;
            }

            $label = $setting['AssetLibraryFilterLabel'];

            if (!($label instanceof BlockElement)) {
                continue;
            }

            $property = $setting['AssetLibraryFilterProperty'];

            if (!($property instanceof BlockElement)) {
                continue;
            }

            $filterName = $property->getData();

            if (!$filterName) {
                continue;
            }

            if (!isset($mappedFilters[$filterName])) {
                continue;
            }

            if (!isset($mappedFilters[$filterName]['type'])) {
                continue;
            }

            if ($mappedFilters[$filterName]['type'] !== 'keyword') {
                continue;
            }

            $aggregationNames[] = $filterName;
        }

        return $aggregationNames;
    }
}
