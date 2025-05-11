<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Exception;

final class MappingDoesNotMatchException extends \RuntimeException
{
    /**
     * @param array<int, array<string, string>>|array<string, mixed> $mappedItem
     * @param array<int, array<string, string>>|array<string, mixed> $mapping
     */
    public static function mismatch(array $mappedItem, array $mapping): MappingDoesNotMatchException
    {
        return new self(sprintf(
            'Mapping config does not match the mapper. Differences are %s',
            implode(',', array_diff(array_keys($mappedItem), array_keys($mapping)))
        ));
    }
}
