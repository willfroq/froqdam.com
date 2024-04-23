<?php

namespace Froq\AssetBundle\Utility;

use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Pimcore\Model\DataObject;

final class IsPathExists
{
    public function __invoke(SwitchUploadRequest $switchUploadRequest, string $objectKey, string $objectPath): bool
    {
        return (new DataObject\Listing())
            ->addConditionParam('o_key = ?', $objectKey)
            ->addConditionParam('o_path = ?', $objectPath)
            ->current() instanceof DataObject;
    }
}
