<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Action;

use Pimcore\Model\Asset\Folder as AssetFolder;
use Pimcore\Model\Asset\Service as AssetService;
use Pimcore\Model\DataObject\Folder as DataObjectFolder;
use Pimcore\Model\Document\Folder as DocumentFolder;

final class GetAssetFolderParent
{
    /**
     * @throws \Exception
     */
    public function __invoke(string $assetFolder): AssetFolder|DocumentFolder|DataObjectFolder
    {
        $folder = AssetFolder::getByPath($assetFolder);

        if (!$folder) {
            $folder = AssetService::createFolderByPath($assetFolder);
        }

        return $folder;
    }
}
