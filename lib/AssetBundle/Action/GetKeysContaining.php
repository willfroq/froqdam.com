<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Action;

final class GetKeysContaining
{
    /**
     * @param  array<int|string, mixed> $haystack
     *
     * @return  array<int|string, mixed>
     */
    public function __invoke(array $haystack, string $searchTerm): array
    {
        $keys = [];

        foreach ($haystack as $key => $value) {
            if (str_contains((string) $key, $searchTerm)) {
                $keys[] = $key;
            }

            if (is_array($value)) {
                $keys = array_merge($keys, ($this)($value, $searchTerm));
            }
        }

        return $keys;
    }
}
