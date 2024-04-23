<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Exception;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Utility\IsPathExists;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Supplier;

final class BuildSupplierFromPayload
{
    public function __construct(
        private readonly IsPathExists $isPathExists,
        private readonly ApplicationLogger $logger,
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

        $payload = (array) json_decode($switchUploadRequest->supplierData, true);

        if (!isset($payload['supplierCode'])) {
            return;
        }

        $supplierCode = (string) $payload['supplierCode'];

        $supplier = new Supplier();

        $supplier->setCode($supplierCode);

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

        $supplierPath = $rootSupplierFolder.AssetResourceOrganizationFolderNames::Suppliers->name.'/';

        if (($this->isPathExists)($switchUploadRequest, $supplierCode, $supplierPath)) {
            $message = sprintf('Related supplier NOT created. %s path already exists, this has to be unique.', $supplierPath);

            $actions[] = $message;

            $this->logger->error(message: $message . implode(separator: ',', array: $actions), context: [
                'component' => $switchUploadRequest->eventName
            ]);
        }

        if (!($this->isPathExists)($switchUploadRequest, $supplierCode, $supplierPath)) {
            $supplier->setPublished(true);
            $supplier->setParentId((int) $parentSupplierFolder->getId());
            $supplier->setKey((string) $payload['supplierCode']);

            $supplier->save();
        }
    }
}
