<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\DataTransferObject;

use Froq\PortalBundle\Opensearch\ValueObject\DateRangeFilter;
use Froq\PortalBundle\Opensearch\ValueObject\InputFilter;
use Froq\PortalBundle\Opensearch\ValueObject\MultiselectCheckboxFilter;
use Froq\PortalBundle\Opensearch\ValueObject\NumberRangeFilter;
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

        /** @var array<string, DateRangeFilter|InputFilter|MultiselectCheckboxFilter|NumberRangeFilter|void>|null $filterValueObjects */
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

        #[Blank(message: '$searchIndex can be blank.')]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public string $searchIndex = '',
    ) {
    }
}
