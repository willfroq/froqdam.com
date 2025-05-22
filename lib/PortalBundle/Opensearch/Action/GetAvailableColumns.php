<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action;

use Pimcore\Model\DataObject\User;

final class GetAvailableColumns
{
    /** @return  array<int, string> */
    public function __invoke(User $user): array
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

            'brands_text',
            'markets_text',
            'mediums_text',
            'substrates_text',
            'printing_techniques_text',

            'organization_id',
            'organizations',

            'image_id',
            'image_filename',

            'category_ids',

            'colour_ids',
            'colour_names',
            'colour_fields_keys',
            'colour_fields_values',

            'print_guidelines_ids',
            'print_guidelines_names',
            'print_guidelines_descriptions',
            'print_guidelines_composite_ids',
        ];
    }
}
