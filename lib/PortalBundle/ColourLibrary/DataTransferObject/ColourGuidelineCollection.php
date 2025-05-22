<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\DataTransferObject;

use Froq\PortalBundle\Opensearch\ValueObject\Aggregation;
use Symfony\Component\Validator\Constraints as Assert;

final class ColourGuidelineCollection
{
    public function __construct(
        #[Assert\Type(type: 'int', message: 'Expected "totalCount" to be a numeric, got %s')]
        public int $totalCount,

        /** @var array<int, ColourGuidelineItem> */
        #[Assert\Type(type: 'array', message: 'Expected "items" to be an array, got %s')]
        public array $items,

        /** @var array<int, Aggregation> */
        #[Assert\Type(type: 'array', message: 'Expected "aggregations" to be an array, got %s')]
        public array $aggregations,
    ) {
    }
}
