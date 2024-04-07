<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Action;

final class BuildNetContentsRequestFilter
{
    /**
     * @param array<int, array<string, array<string, string>>> $requestFilters
     *
     * @return array<string, array<string, float>>>
     */
    public function __invoke(array $requestFilters, string $filterName, bool $isNetContents): array
    {
        $min = [];
        $max = [];

        if (!$isNetContents) {
            return [];
        }

        foreach ($requestFilters as $requestFilter) {
            if (isset($requestFilter[$filterName]['min'])) {
                $min = [
                    'min' => (float) $requestFilter[$filterName]['min'],
                ];
            }

            if (isset($requestFilter[$filterName]['max'])) {
                $max = [
                    'max' => (float) $requestFilter[$filterName]['max'],
                ];
            }
        }

        $result = array_merge($min, $max);

        return [$filterName => $result];
    }
}
