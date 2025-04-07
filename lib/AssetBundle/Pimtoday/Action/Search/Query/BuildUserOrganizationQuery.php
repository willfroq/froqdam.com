<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Action\Search\Query;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Terms as TermsQuery;
use Pimcore\Model\DataObject\Organization;

final class BuildUserOrganizationQuery
{
    public function __invoke(Query $query, BoolQuery $boolQuery, Organization $organization): void
    {
        $boolQuery->addFilter(new TermsQuery('organization_id', array_values(array_filter([$organization->getId()]))));

        $query->setQuery($boolQuery);

        $query->setParam('track_total_hits', true);
    }
}
