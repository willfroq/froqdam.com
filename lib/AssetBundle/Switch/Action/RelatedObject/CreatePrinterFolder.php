<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\RelatedObject;

use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Organization;

final class CreatePrinterFolder
{
    /**
     * @throws \Exception
     */
    public function __invoke(Organization $organization, string $path): DataObject
    {
        $containerFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', (string) $organization->getKey())
            ->addConditionParam('o_path = ?', '/Customers/')
            ->current();

        if (!($containerFolder instanceof DataObject\Folder)) {
            throw new \Exception(message: 'CreatePrinterFolder: No container folder i.e. /Customers/org-name');
        }

        $parentPrinterFolder = new DataObject\Folder();
        $parentPrinterFolder->setKey(AssetResourceOrganizationFolderNames::Printers->readable());
        $parentPrinterFolder->setPath($path);
        $parentPrinterFolder->setParentId((int) $containerFolder->getId());

        try {
            $parentPrinterFolder->save();
        } catch (\Exception $exception) {
            throw new \Exception(message: $exception->getMessage());
        }

        return $parentPrinterFolder;
    }
}
