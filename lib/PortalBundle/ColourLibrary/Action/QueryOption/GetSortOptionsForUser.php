<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Action\QueryOption;

use Froq\PortalBundle\Opensearch\ValueObject\SortOption;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class GetSortOptionsForUser
{
    /**
     * @param User $user
     * @param array<int, string> $sortableNames
     *
     * @return array<int, SortOption>
     */
    public function __invoke(array $sortableNames, #[CurrentUser] User $user): array
    {
        // TODO: This has to be dynamic later. Admin should be able to configure which field a user can sort, query, aggregate, search, filter etc.
        $sortOptions = [];

        foreach ($sortableNames as $sortableName) {
            $label = str_contains($sortableName, '_') ? ucfirst((string) strstr($sortableName, '_', true)) : ucfirst($sortableName);

            $sortOptions[] = new SortOption(label: $label, filterName: $sortableName, sortDirection: 'asc');
            $sortOptions[] = new SortOption(label: $label, filterName: $sortableName, sortDirection: 'desc');
        }

        return $sortOptions;
    }
}
