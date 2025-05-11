<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action;

use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest;
use Froq\PortalBundle\Opensearch\Action\Factory\GetItemNamesFactory;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GetColumnMappingForUser
{
    public function __construct(
        private readonly GetYamlConfigFileProperties $getYamlConfigFileProperties,
        private readonly GetAvailableColumns $getAvailableColumns,
        private readonly GetItemNamesFactory $getItemNamesFactory
    ) {
    }

    /**
     * @return array<string, array<string, string>>
     *
     * @throws \Exception
     */
    public function __invoke(SearchRequest $searchRequest, #[CurrentUser] User $user): array
    {
        $availableColumns = array_merge(
            ($this->getAvailableColumns)($user),
            ($this->getItemNamesFactory->create($searchRequest->searchIndex))()
        );

        $result = [];

        foreach (($this->getYamlConfigFileProperties)($searchRequest->searchIndex) as $filterName => $property) {
            if (!isset($property['type'])) {
                continue;
            }

            if (!in_array($filterName, $availableColumns, true)) {
                continue;
            }

            $result[$filterName] = $property;
        }

        return $result;
    }
}
