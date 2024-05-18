<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Utility;

final class ImplodeAssociativeArray
{
    /** @param  array<string, mixed> $associativeArray */
    public function __invoke(array $associativeArray, string $kvSeparator = '=', string $pairSeparator = ', ', int $level = 0, int $maxLevel = 3): string
    {
        $pairs = [];

        foreach ($associativeArray as $key => $value) {
            if ($level > $maxLevel) {
                continue;
            }

            if (!$value) {
                continue;
            }

            if (is_array($value)) {
                $pairs[] = str_repeat('  ', $level) . $key . $kvSeparator . '{' . ($this)($value, $kvSeparator, $pairSeparator, $level + 1) . '}';
            }

            if (!is_array($value)) {
                $pairs[] = str_repeat(' ', $level) . $key . $kvSeparator . $value;
            }
        }

        return implode($pairSeparator, $pairs);
    }
}
