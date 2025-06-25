<?php

declare(strict_types=1);

namespace Froq\PortalBundle\AssetLibrary\Action\QueryOption;

use Froq\PortalBundle\Opensearch\Action\GetYamlConfigFileProperties;
use Pimcore\Model\DataObject\Data\BlockElement;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\User;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GetFilterMappingForUser
{
    public function __construct(private readonly GetYamlConfigFileProperties $getYamlConfigFileProperties)
    {
    }

    /**
     * @return array<string, array<string, string>>
     *
     * @throws \Exception
     * @throws InvalidArgumentException
     */
    public function __invoke(string $indexName, #[CurrentUser] User $user): array
    {
        $settings = $user->getGroupAssetLibrarySettings();

        if (!($settings instanceof GroupAssetLibrarySettings)) {
            return [];
        }

        $filterNamesForUser = [];

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

            $filterNamesForUser[] = $filterName;
        }

        $mappedFilters = ($this->getYamlConfigFileProperties)($indexName);

        $result = [];

        foreach ($mappedFilters as $filterName => $property) {
            if (!isset($property['type'])) {
                continue;
            }

            if (!in_array(needle: (string) $filterName, haystack: $filterNamesForUser, strict: true)) {
                continue;
            }

            $result[$filterName] = $property;
        }

        return $result;
    }
}
