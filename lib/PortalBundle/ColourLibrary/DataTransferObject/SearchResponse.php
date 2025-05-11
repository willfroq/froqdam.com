<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\DataTransferObject;

use Symfony\Component\Validator\Constraints as Assert;

final class SearchResponse
{
    public function __construct(
        /** @var array<int, ColourGuidelineItem> */
        #[Assert\Type(type: 'array', message: 'Expected "projectItems" to be a array, got {{ type }}')]
        public array $colourGuidelineItems,

        #[Assert\Type(type: 'int', message: 'Expected "totalCount" to be a int, got {{ type }}')]
        public int $totalCount,

        /** @var array<int, string> */
        #[Assert\Type(type: 'array', message: 'Expected "aggregationNames" to be a array, got {{ type }}')]
        public array $aggregationNames,

        /** @var array<int, Aggregation> */
        #[Assert\Type(type: 'array', message: 'Expected "aggregations" to be an array, got %s')]
        public array $aggregationDtos,

        /** @var array<string, int> */
        #[Assert\Type(type: 'array', message: 'Expected "paginator" to be a array, got {{ type }}')]
        public array $paginator,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'colourGuidelineItems' => $this->colourGuidelineItems,
            'totalCount' => $this->totalCount,
            'aggregationNames' => $this->aggregationNames,
            'aggregationDtos' => $this->aggregationDtos,
            'paginator' => $this->paginator,
        ];
    }
}
