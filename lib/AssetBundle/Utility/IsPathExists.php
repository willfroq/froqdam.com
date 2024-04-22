<?php

namespace Froq\AssetBundle\Utility;

use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Pimcore\Model\DataObject;

final class IsPathExists
{
    public function __invoke(SwitchUploadRequest $switchUploadRequest, string $objectPath): bool
    {
        $newParentPath = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', $switchUploadRequest->filename)
            ->addConditionParam('o_path = ?', $objectPath)
            ->current();

        return $newParentPath instanceof DataObject;
    }
}
