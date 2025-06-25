<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Action\QueryOption;

use Froq\PortalBundle\Opensearch\ValueObject\Column;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GetColumnForUser
{
    /** @return  array<int, Column> */
    public function __invoke(#[CurrentUser] User $user, string $sortDirection, string $sortBy): array
    {
        // TODO: This has to be dynamic later. Admin should be able to configure which field a user can sort, query, aggregate, search, filter etc.
        return [
            new Column(
                label: 'Markets',
                filterName: 'markets',
                sortDirection: 'markets' === $sortBy ? $sortDirection : ''
            ),
            new Column(
                label: 'Brands',
                filterName: 'brands',
                sortDirection: 'brands' === $sortBy ? $sortDirection : ''
            ),
            new Column(
                label: 'Substrates',
                filterName: 'substrates',
                sortDirection: 'substrates' === $sortBy ? $sortDirection : ''
            ),
            new Column(
                label: 'Printing Technique',
                filterName: 'printing_techniques',
                sortDirection: 'printing_technique' === $sortBy ? $sortDirection : ''
            ),

            new Column(
                label: 'Mediums',
                filterName: 'mediums',
                sortDirection: 'mediums' === $sortBy ? $sortDirection : ''
            ),
        ];
    }
}
