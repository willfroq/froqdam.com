<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Utility;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

final class FlattenArray
{
    /**
     * @param array<int, mixed> $nestedArray
     * @param bool $preserveKey
     *
     * @return array<int, mixed>
     */
    public function __invoke(array $nestedArray, bool $preserveKey = false): array
    {
        return iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($nestedArray)), $preserveKey);
    }
}
