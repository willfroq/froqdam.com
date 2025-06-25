<?php

declare(strict_types=1);

namespace Froq\PortalBundle\AssetLibrary\DataTransferObject;

use Froq\PortalBundle\Opensearch\ValueObject\Column;
use Froq\PortalBundle\Opensearch\ValueObject\DateRangeFilter;
use Froq\PortalBundle\Opensearch\ValueObject\InputFilter;
use Froq\PortalBundle\Opensearch\ValueObject\MultiselectCheckboxFilter;
use Froq\PortalBundle\Opensearch\ValueObject\NumberRangeFilter;
use Froq\PortalBundle\Opensearch\ValueObject\SidebarFilter;
use Froq\PortalBundle\Opensearch\ValueObject\SortOption;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Blank;

final class SearchRequest
{
    public function __construct(
        #[Blank(message: '$query can be blank.')]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public ?string $query,

        #[Blank(message: '$page can be blank.')]
        #[Assert\Type(type: 'int', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public ?int $page,

        #[Blank(message: '$size can be blank.')]
        #[Assert\Type(type: 'int', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public ?int $size,

        #[Blank(message: '$sortBy can be blank.')]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public ?string $sortBy,

        #[Blank(message: '$sortDirection can be blank.')]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public ?string $sortDirection,

        /** @var array<int, string>|null $filters */
        #[Blank(message: '$filters can be blank.')]
        #[Assert\Type(type: 'array', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public ?array $filters,

        /** @var array<string, DateRangeFilter|InputFilter|MultiselectCheckboxFilter|NumberRangeFilter>|null $filterValueObjects */
        #[Blank(message: '$filterValueObjects can be blank.')]
        #[Assert\Type(type: 'array', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public ?array $filterValueObjects,

        public ?bool $hasErrors,

        #[Blank(message: '$hasAggregation can be blank.')]
        #[Assert\Type(type: 'bool', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public ?bool $hasAggregation,

        /** @var array<int, string> $aggregationNames */
        #[Blank(message: '$aggregationName can be blank.')]
        #[Assert\Type(type: 'array', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public array $aggregationNames,

        /** @var array<int, SidebarFilter> $sidebarFilters */
        #[Blank(message: '$sidebarFilters can be blank.')]
        #[Assert\Type(type: 'array', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public array $sidebarFilters,

        /** @var array<int, string> $columnNames */
        #[Blank(message: '$columnNames can be blank.')]
        #[Assert\Type(type: 'array', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public array $columnNames,

        /** @var array<int, Column> $columns */
        #[Blank(message: '$columns can be blank.')]
        #[Assert\Type(type: 'array', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public array $columns,

        /** @var array<int, string> $sortableNames */
        #[Blank(message: '$sortableNames can be blank.')]
        #[Assert\Type(type: 'array', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public array $sortableNames,

        /** @var array<int, string> $querySource */
        #[Blank(message: '$querySource can be blank.')]
        #[Assert\Type(type: 'array', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public array $querySource,

        /** @var array<int, SortOption> $sortOptions */
        #[Blank(message: '$sortOptions can be blank.')]
        #[Assert\Type(type: 'array', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public array $sortOptions,

        #[Blank(message: '$selectedSortOptions can be blank.')]
        public ?SortOption $selectedSortOption,

        #[Blank(message: '$searchIndex can be blank.')]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public string $searchIndex = '',
    ) {
    }
}
