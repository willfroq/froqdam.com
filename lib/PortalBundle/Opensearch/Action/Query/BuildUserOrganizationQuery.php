<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action\Query;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Terms;
use Froq\PortalBundle\ColourLibrary\DataTransferObject\SearchRequest;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class BuildUserOrganizationQuery
{
    public function __invoke(
        Query $query,
        BoolQuery $boolQuery,
        SearchRequest $searchRequest,
        #[CurrentUser] User $user
    ): void {
        $termsQuery = new Terms('organization_id');
        $termsQuery->setTerms(array_filter(array_map(callback: fn (Organization $organization) => $organization->getId(), array: $user->getOrganizations())));

        $boolQuery->addMust($termsQuery);

        $query->setQuery($boolQuery);
        $query->setQuery($termsQuery);

        $query->setParam('track_total_hits', true);
    }
}
