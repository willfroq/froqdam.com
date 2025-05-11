<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action\Aggregation;

use Elastica\Aggregation\Terms as TermsAggregation;
use Elastica\Query;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest;
use Froq\PortalBundle\Opensearch\Action\Factory\GetItemNamesFactory;
use Froq\PortalBundle\Opensearch\Action\Filter\GetFilterMappingForUser;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class BuildAggregation
{
    public function __construct(
        private readonly GetFilterMappingForUser $getFilterMappingForUser,
        private readonly GetItemNamesFactory $getItemNamesFactory
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Query $query, SearchRequest $searchRequest, #[CurrentUser] User $user): void
    {
        $aggregationNames = array_intersect(
            array_keys(($this->getFilterMappingForUser)($user, $searchRequest->searchIndex)),
            (array)($this->getItemNamesFactory->create($searchRequest->searchIndex))()
        );

        if (count(array_intersect($aggregationNames, $searchRequest->aggregationNames)) <= 1) {
            return;
        }

        foreach ($searchRequest->aggregationNames as $aggregationName) {
            $termsAggregation = new TermsAggregation((string) $aggregationName);

            $termsAggregation->setField((string) $aggregationName);
            $termsAggregation->setSize(100);

            $query->addAggregation($termsAggregation);
        }
    }
}
