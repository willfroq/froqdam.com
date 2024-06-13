<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\RelatedObject;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Organization;

final class CreateCategoryFolderLevelLabel
{
    /**
     * @throws \Exception
     */
    public function __invoke(Organization $organization, DataObject $parentCategoryFolder, string $levelLabelName): DataObject
    {

        $parentCategoryFolderLevelLabel = new DataObject\Folder();
        $parentCategoryFolderLevelLabel->setKey($levelLabelName);
        $parentCategoryFolderLevelLabel->setPath((string) $parentCategoryFolder->getPath());
        $parentCategoryFolderLevelLabel->setParentId((int) $parentCategoryFolder->getId());

        try {
            $parentCategoryFolderLevelLabel->save();
        } catch (\Exception $exception) {
            throw new \Exception(message: $exception->getMessage());
        }

        return $parentCategoryFolderLevelLabel;
    }
}
