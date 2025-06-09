<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action;

use Froq\PortalBundle\Opensearch\Contract\GetFilterNamesInterface;

final class GetColourGuidelineItemFilterNames implements GetFilterNamesInterface
{
    /** @return  array<int, string> */
    public function __invoke(): array
    {
        // TODO: This has to be dynamic later. Admin should be able to configure which field a user can sort, query, aggregate, search, filter etc.
        return [
            'colourGuidelineId',
            'name',
            'imageId',
            'countries',

            'created_at_timestamp',
            'updated_at_timestamp',
            'description',

            'brands',
            'markets',
            'mediums',
            'substrates',
            'printing_techniques',

            'organization_id',
            'organizations',

            'image_id',
            'image_filename',

            'colour_ids',
            'colour_names',

            'print_guidelines_ids',
            'print_guidelines_names',
            'print_guidelines_descriptions',
        ];
    }
}
