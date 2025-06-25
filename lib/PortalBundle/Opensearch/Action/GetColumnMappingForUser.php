<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action;

use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GetColumnMappingForUser
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
    public function __invoke(SearchRequest $searchRequest, #[CurrentUser] User $user): array
    {
        $availableColumns = [];

        $result = [];

        foreach (($this->getYamlConfigFileProperties)($searchRequest->searchIndex) as $filterName => $property) {
            if (!isset($property['type'])) {
                continue;
            }

            $result[$filterName] = $property;
        }

        return $result;
    }
}
