<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action\Sort;

use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GetAvailableSorts
{
    /** @return  array<int, string> */
    public function __invoke(#[CurrentUser] User $user): array
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

            'brand_names',
            'market_names',
            'medium_names',
            'substrate_names',
            'printing_technique_names',

            'organization_id',
            'organization_name',

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
