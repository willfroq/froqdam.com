<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Exception;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Supplier;

final class BuildSupplierFromPayload
{
    /**
     * @throws Exception
     * @throws \Exception
     */
    public function __invoke(SwitchUploadRequest $switchUploadRequest, Organization $organization): void
    {
        $rootSupplierFolder = $organization->getObjectFolder() . '/';

        $parentSupplierFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Suppliers->name)
            ->addConditionParam('o_path = ?', $rootSupplierFolder)
            ->current();

        if (!($parentSupplierFolder instanceof DataObject)) {
            return;
        }

        $payload = (array) json_decode($switchUploadRequest->supplierData, true);

        if (!isset($payload['supplierCode'])) {
            return;
        }

        $supplier = new Supplier();

        $supplier->setCode($payload['supplierCode']);

        if (isset($payload['supplierCompany'])) {
            $supplier->setCompany($payload['supplierCompany']);
        }
        if (isset($payload['supplierContact'])) {
            $supplier->setContact($payload['supplierContact']);
        }
        if (isset($payload['supplierStreetName'])) {
            $supplier->setStreetName($payload['supplierStreetName']);
        }
        if (isset($payload['supplierStreetNumber'])) {
            $supplier->setStreetNumber($payload['supplierStreetNumber']);
        }
        if (isset($payload['supplierPostalCode'])) {
            $supplier->setPostalCode($payload['supplierPostalCode']);
        }
        if (isset($payload['supplierCity'])) {
            $supplier->setCity($payload['supplierCity']);
        }
        if (isset($payload['supplierPhoneNumber'])) {
            $supplier->setPhoneNumber($payload['supplierPhoneNumber']);
        }
        if (isset($payload['supplierEmail'])) {
            $supplier->setEmail($payload['supplierEmail']);
        }

        $supplier->setPublished(true);
        $supplier->setParentId((int) $parentSupplierFolder->getId());
        $supplier->setKey((string) $payload['supplierCode']);

        $supplier->save();
    }
}
