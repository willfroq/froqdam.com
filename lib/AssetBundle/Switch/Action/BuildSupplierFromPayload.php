<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Exception;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\ValueObject\SupplierFromPayload;
use Froq\AssetBundle\Utility\IsPathExists;
use Froq\PortalBundle\Api\ValueObject\ValidationError;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Supplier;

final class BuildSupplierFromPayload
{
    public function __construct(
        private readonly IsPathExists $isPathExists,
    ) {
    }

    /**
     * @throws Exception
     * @throws \Exception
     *
     * @param array<int, string> $actions
     */
    public function __invoke(SwitchUploadRequest $switchUploadRequest, Organization $organization, array $actions): void
    {
        $rootSupplierFolder = $organization->getObjectFolder() . '/';

        $parentSupplierFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Suppliers->name)
            ->addConditionParam('o_path = ?', $rootSupplierFolder)
            ->current();

        if (!($parentSupplierFolder instanceof DataObject)) {
            return;
        }

        $supplierData = (array) json_decode($switchUploadRequest->supplierData, true);
        $supplierFromPayload = new SupplierFromPayload(
            supplierCode: $supplierData['supplierCode'] ?? null,
            supplierCompany: $supplierData['supplierCompany'] ?? null,
            supplierContact: $supplierData['supplierContact'] ?? null,
            supplierStreetName: $supplierData['supplierStreetName'] ?? null,
            supplierStreetNumber: $supplierData['supplierStreetNumber'] ?? null,
            supplierPostalCode: $supplierData['supplierPostalCode'] ?? null,
            supplierPhoneNumber: $supplierData['supplierPhoneNumber'] ?? null,
            supplierEmail: $supplierData['supplierEmail'] ?? null,
        );

        $supplierCode = (string) $supplierFromPayload->supplierCode;

        $supplierPath = $rootSupplierFolder.AssetResourceOrganizationFolderNames::Suppliers->name.'/';
        $supplierKey = $supplierCode;

        if (!($this->isPathExists)($supplierCode, $supplierPath)) {
            $supplier = new Supplier();

            $supplier->setCode($supplierCode);
            $supplier->setCompany($supplierFromPayload->supplierCompany);
            $supplier->setContact($supplierFromPayload->supplierContact);
            $supplier->setStreetName($supplierFromPayload->supplierStreetName);
            $supplier->setStreetNumber($supplierFromPayload->supplierStreetNumber);
            $supplier->setPostalCode($supplierFromPayload->supplierPostalCode);
            $supplier->setPhoneNumber($supplierFromPayload->supplierPhoneNumber);
            $supplier->setEmail($supplierFromPayload->supplierEmail);

            $supplier->setPublished(true);
            $supplier->setParentId((int) $parentSupplierFolder->getId());
            $supplier->setKey($supplierCode);

            $supplier->save();
        }
    }
}
