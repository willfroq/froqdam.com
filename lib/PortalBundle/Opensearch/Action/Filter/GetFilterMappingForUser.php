<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action\Filter;

use Froq\PortalBundle\Opensearch\Action\Factory\GetItemNamesFactory;
use Froq\PortalBundle\Opensearch\Action\GetYamlConfigFileProperties;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GetFilterMappingForUser
{
    public function __construct(
        private readonly GetYamlConfigFileProperties $getYamlConfigFileProperties,
        private readonly GetAvailableFilters $getAvailableFilters,
        private readonly GetItemNamesFactory $getItemNamesFactory
    ) {
    }

    /**
     * @return array<string, array<string, string>>
     *
     * @throws \Exception
     */
    public function __invoke(#[CurrentUser] User $user, string $indexName): array
    {
        $availableFilters = array_merge(
            ($this->getAvailableFilters)($user, $indexName),
            ($this->getItemNamesFactory->create($indexName))()
        );

        $result = [];

        foreach (($this->getYamlConfigFileProperties)($indexName) as $filterName => $property) {
            if (!isset($property['type'])) {
                continue;
            }

            if (!in_array(needle: $filterName, haystack: $availableFilters, strict: true)) {
                continue;
            }

            $result[$filterName] = $property;
        }

        return $result;
    }
}
