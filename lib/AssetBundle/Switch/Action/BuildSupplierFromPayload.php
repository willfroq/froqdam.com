<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Exception;
use Froq\AssetBundle\Switch\Action\RelatedObject\CreateSupplierFolder;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\ValueObject\SupplierFromPayload;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Supplier;

final class BuildSupplierFromPayload
{
    public function __construct(
        private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull,
        private readonly CreateSupplierFolder $createSupplierFolder,
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

        $supplierData = (array) json_decode($switchUploadRequest->supplierData, true);

        if (!isset($supplierData['supplierCode'])) {
            return;
        }

        if (!($parentSupplierFolder instanceof DataObject)) {
            $parentSupplierFolder = ($this->createSupplierFolder)($organization, $rootSupplierFolder);
        }

        if (($this->allPropsEmptyOrNull)($supplierData)) {
            return;
        }

        $supplierFromPayload = new SupplierFromPayload(
            supplierCode: $supplierData['supplierCode'] ?? '',
            supplierCompany: $supplierData['supplierCompany'] ?? '',
            supplierContact: $supplierData['supplierContact'] ?? '',
            supplierStreetName: $supplierData['supplierStreetName'] ?? '',
            supplierStreetNumber: $supplierData['supplierStreetNumber'] ?? '',
            supplierPostalCode: $supplierData['supplierPostalCode'] ?? '',
            supplierPhoneNumber: $supplierData['supplierPhoneNumber'] ?? '',
            supplierEmail: $supplierData['supplierEmail'] ?? '',
        );

        $supplierPath = $rootSupplierFolder.AssetResourceOrganizationFolderNames::Suppliers->readable().'/';

        $supplier = (new Supplier\Listing())
            ->addConditionParam('o_key = ?', $supplierFromPayload->supplierCode)
            ->addConditionParam('o_path = ?', $supplierPath)
            ->addConditionParam('Code = ?', $supplierFromPayload->supplierCode)
            ->addConditionParam('Company = ?', $supplierFromPayload->supplierCompany)
            ->addConditionParam('Contact = ?', $supplierFromPayload->supplierContact)
            ->addConditionParam('StreetName = ?', $supplierFromPayload->supplierStreetName)
            ->addConditionParam('StreetNumber = ?', $supplierFromPayload->supplierStreetNumber)
            ->addConditionParam('PostalCode = ?', $supplierFromPayload->supplierPostalCode)
            ->addConditionParam('Email = ?', $supplierFromPayload->supplierEmail)
            ->current();

        if (!($supplier instanceof Supplier)) {
            $supplier = new Supplier();

            $supplier->setCode($supplierFromPayload->supplierCode);
            $supplier->setCompany($supplierFromPayload->supplierCompany);
            $supplier->setContact($supplierFromPayload->supplierContact);
            $supplier->setStreetName($supplierFromPayload->supplierStreetName);
            $supplier->setStreetNumber($supplierFromPayload->supplierStreetNumber);
            $supplier->setPostalCode($supplierFromPayload->supplierPostalCode);
            $supplier->setPhoneNumber($supplierFromPayload->supplierPhoneNumber);
            $supplier->setEmail($supplierFromPayload->supplierEmail);

            $supplier->setPublished(true);
            $supplier->setParentId((int) $parentSupplierFolder->getId());
            $supplier->setKey((string) $supplierFromPayload->supplierCode);

            $supplier->save();

            return;
        }

        if (empty($supplier->getCode())) {
            $supplier->setCode($supplierFromPayload->supplierCode);
        }

        if (empty($supplier->getCompany())) {
            $supplier->setCompany($supplierFromPayload->supplierCompany);
        }

        if (empty($supplier->getContact())) {
            $supplier->setContact($supplierFromPayload->supplierContact);
        }

        if (empty($supplier->getStreetName())) {
            $supplier->setStreetName($supplierFromPayload->supplierStreetName);
        }

        if (empty($supplier->getStreetNumber())) {
            $supplier->setStreetNumber($supplierFromPayload->supplierStreetNumber);
        }

        if (empty($supplier->getPostalCode())) {
            $supplier->setPostalCode($supplierFromPayload->supplierPostalCode);
        }

        if (empty($supplier->getPhoneNumber())) {
            $supplier->setPhoneNumber($supplierFromPayload->supplierPhoneNumber);
        }

        if (empty($supplier->getEmail())) {
            $supplier->setEmail($supplierFromPayload->supplierEmail);
        }

        $supplier->setPublished(true);
        $supplier->setParentId((int) $parentSupplierFolder->getId());
        $supplier->setKey((string) $supplierFromPayload->supplierCode);

        $supplier->save();
    }
}
