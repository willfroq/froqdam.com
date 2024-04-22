<?php

namespace Froq\AssetBundle\Utility;

final class AreAllPropsEmptyOrNull
{
    /** @param  array<string, mixed> $array */
    public function __invoke(array $array): bool
    {
        foreach ($array as $value) {
            if ($value !== '' && $value !== null) {
                return false;
            }
        }

        return true;
    }
}
