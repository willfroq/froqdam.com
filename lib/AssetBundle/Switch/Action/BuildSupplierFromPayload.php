<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Exception;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Pimcore\Model\DataObject\Supplier;

final class BuildSupplierFromPayload
{
    public function __construct(private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull)
    {
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function __invoke(SwitchUploadRequest $switchUploadRequest): void
    {
        $payload = (array) json_decode($switchUploadRequest->supplierData, true);

        if (!isset($payload['supplierCode'])) {
            return;
        }

        if (empty($payload) || ($this->allPropsEmptyOrNull)($payload)) {
            return;
        }

        $supplier = new Supplier();

        if (isset($payload['supplierCode'])) {
            $supplier->setCode($payload['supplierCode']);
        }
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

        $supplier->save();
    }
}