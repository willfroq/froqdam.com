<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Utility;

use Pimcore\Model\DataObject;

final class IsPathExists
{
    public function __invoke(string $objectKey, string $objectPath): bool
    {
        return (new DataObject\Listing())
            ->addConditionParam('o_key = ?', $objectKey)
            ->addConditionParam('o_path = ?', $objectPath)
            ->current() instanceof DataObject;
    }
}
