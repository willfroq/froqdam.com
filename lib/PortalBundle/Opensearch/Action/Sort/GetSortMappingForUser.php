<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action\Sort;

use Froq\PortalBundle\Opensearch\Action\GetYamlConfigFileProperties;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GetSortMappingForUser
{
    public function __construct(
        private readonly GetYamlConfigFileProperties $getYamlConfigFileProperties,
    ) {
    }

    /**
     * @return array<string, array<string, string>>
     *
     * @throws \Exception
     */
    public function __invoke(#[CurrentUser] User $user, string $indexName): array
    {
        $availableSorts = [];

        $result = [];

        foreach (($this->getYamlConfigFileProperties)($indexName) as $filterName => $property) {
            if (!isset($property['type'])) {
                continue;
            }

            $result[$filterName] = $property;
        }

        return $result;
    }
}
