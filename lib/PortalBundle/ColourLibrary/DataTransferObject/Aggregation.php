<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\DataTransferObject;

use Symfony\Component\Validator\Constraints as Assert;

final class Aggregation
{
    public function __construct(
        #[Assert\Type(type: 'string', message: 'Expected "fieldName" to be a string, got {{ type }}')]
        public string $fieldName,
        #[Assert\Type(type: 'bool', message: 'Expected "hasError" to be a bool, got {{ type }}')]
        public bool $hasError,
        #[Assert\Type(type: 'int', message: 'Expected "sumOfDocCount" to be a int, got {{ type }}')]
        public int $sumOfDocCount,
        /** @var array<int, array<string, mixed>> */
        #[Assert\Type(type: 'array', message: 'Expected "buckets" to be a array, got {{ type }}')]
        public array $buckets,
    ) {
    }
}
