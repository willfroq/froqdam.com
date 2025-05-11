<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action\Filter;

use Froq\PortalBundle\Opensearch\Action\GetYamlConfigFileProperties;
use Froq\PortalBundle\Opensearch\Action\Sort\GetAvailableSorts;
use Pimcore\Model\DataObject\User;

final class GetAvailableFilters
{
    public function __construct(
        private readonly GetYamlConfigFileProperties $getYamlConfigFileProperties,
        private readonly GetAvailableSorts $getAvailableSorts
    ) {
    }

    /**
     * @return array<string, string>
     *
     * @throws \Exception
     */
    public function __invoke(User $user, string $indexName): array
    {
        $result = [];

        $availableFilters = ($this->getAvailableSorts)($user);

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
