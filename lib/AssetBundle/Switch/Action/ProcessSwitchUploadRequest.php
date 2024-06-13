<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\ValueObject\CategoryFromPayload;
use Froq\AssetBundle\Switch\ValueObject\PrinterFromPayload;
use Froq\AssetBundle\Switch\ValueObject\ProductFromPayload;
use Froq\AssetBundle\Switch\ValueObject\ProjectFromPayload;
use Froq\AssetBundle\Switch\ValueObject\SupplierFromPayload;
use Froq\AssetBundle\Switch\ValueObject\TagFromPayload;
use Froq\AssetBundle\Utility\IsAssetAndAssetResourceInSync;
use Froq\AssetBundle\Utility\IsPathExists;
use Froq\AssetBundle\Utility\IsProjectExists;
use Froq\AssetBundle\Utility\IsTagExists;
use Froq\PortalBundle\Api\ValueObject\ValidationError;
use Pimcore\Model\DataObject\Organization;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

final class ProcessSwitchUploadRequest
{
    public function __construct(
        private readonly IsPathExists $isPathExists,
        private readonly IsProjectExists $isProjectExists,
        private readonly IsTagExists $isTagExists,
        private readonly IsAssetAndAssetResourceInSync $isAssetAndAssetResourceInSync,
    ) {
    }

    /** @param array<int, ValidationError> $errors
     * @throws \Exception
     */
    public function __invoke(Request $request, ?UploadedFile $file, ?string &$customAssetFolder, array &$errors): void
    {
        if (!($file instanceof UploadedFile)) {
            $errors[] = new ValidationError(propertyPath: 'fileContents', message: sprintf('FileContents %s is not a file.', $file));
        }

        $assetFolder = $request->request->get('customAssetFolder');

        $customAssetFolder = $assetFolder === null || $assetFolder === '' ? AssetResourceOrganizationFolderNames::Assets->name : $assetFolder;

        $organization = Organization::getByCode((string) $request->request->get('customerCode'))->current(); /** @phpstan-ignore-line */
        if (!($organization instanceof Organization)) {
            return;
        }

        $rootAssetResourceFolder = $organization->getObjectFolder() . '/';

        $productData = (array) json_decode((string) $request->request->get('productData'), true);
        $categories = $productData['productCategories'] ?? null ;
        $product = new ProductFromPayload(
            productName: $productData['productName'] ?? null,
            productEAN: $productData['productEAN'] ?? null,
            productSKU: $productData['productSKU'] ?? null,
            productAttributes: [],
            productNetContentStatement: $productData['productNetContentStatement'] ?? null,
            productNetContents: [],
            productNetUnitContents: [],
            productCategories: new CategoryFromPayload(
                brand: $categories['brand'] ?? null,
                campaign: $categories['campaign'] ?? null,
                market: $categories['market'] ?? null,
                segment: $categories['segment'] ?? null,
                platform: $categories['platform'] ?? null,
            )
        );

        $productKey = (string) $product->productName;
        $productPath = $rootAssetResourceFolder.AssetResourceOrganizationFolderNames::Products->readable().'/';

        if (!isset($product->productName)) {
            $errors[] = new ValidationError(propertyPath: 'Product', message: sprintf('Product %s has no product name.', $productPath.$productKey));
        }

        if (($this->isPathExists)($productKey, $productPath)) {
            $errors[] = new ValidationError(propertyPath: 'Product', message: sprintf('Product %s path already exists, this has to be unique.', $productPath.$productKey));
        }

        $categoryPath = $rootAssetResourceFolder.AssetResourceOrganizationFolderNames::Categories->readable().'/';

        foreach ($product->productCategories?->toArray() ?? [] as $levelLabelName => $categoryName) {
            $categoryLevelLabelName = ucfirst($levelLabelName).'s';

            if (($this->isPathExists)((string) $categoryName, $categoryPath.$categoryLevelLabelName.'/')) {
                $errors[] = new ValidationError(propertyPath: 'Category', message: sprintf('Category %s path already exists, this has to be unique.', $categoryPath.$categoryLevelLabelName.'/'.$categoryName));
            }
        }

        $tagData = (array) json_decode((string) $request->request->get('tagData'), true);
        $tagPath = $rootAssetResourceFolder.AssetResourceOrganizationFolderNames::Tags->name.'/';

        foreach ($tagData as $tagDatum) {
            $tag = new TagFromPayload(code: $tagDatum['code'] ?? null, name: $tagDatum['name'] ?? null);

            if (($this->isPathExists)((string) $tag->code, $tagPath)) {
                $errors[] = new ValidationError(propertyPath: 'Tag', message: sprintf('Tag %s path already exists, this has to be unique.', $tagPath.$tag->code));
            }

            if (($this->isTagExists)('Code', (string) $tag->code)) {
                $errors[] = new ValidationError(propertyPath: 'Tag', message: sprintf('Tag %s already exists, this has to be unique.', $tag->code));
            }
        }

        $projectData = (array) json_decode((string) $request->request->get('projectData'), true);
        $project = new ProjectFromPayload(
            projectCode: $projectData['projectCode'] ?? null,
            projectName: $projectData['projectName'] ?? null,
            pimProjectNumber: $projectData['pimProjectNumber'] ?? null,
            froqProjectNumber: $projectData['froqProjectNumber'] ?? null,
            customerProjectNumber: $projectData['customerProjectNumber'] ?? null,
            froqName: $projectData['froqName'] ?? null,
            description: $projectData['description'] ?? null,
            projectType: $projectData['projectType'] ?? null,
            status: $projectData['status'] ?? null,
            location: $projectData['location'] ?? null,
            deliveryType: $projectData['deliveryType'] ?? null,
        );

        $projectPath = $rootAssetResourceFolder.AssetResourceOrganizationFolderNames::Projects->name.'/';
        $projectKey = $project->froqName;

        if (($this->isPathExists)((string) $projectKey, $projectPath)) {
            $errors[] = new ValidationError(propertyPath: 'Project', message: sprintf('Project %s path already exists, this has to be unique.', $projectPath.$projectKey));
        }

        if (($this->isProjectExists)('Code', (string) $project->projectCode)) {
            $errors[] = new ValidationError(propertyPath: 'Project', message: sprintf('Project %s already exists, this has to be unique.', $projectKey));
        }

        if (($this->isProjectExists)('pim_project_number', (string) $project->pimProjectNumber)) {
            $errors[] = new ValidationError(propertyPath: 'Project', message: sprintf('Project %s already exists, this has to be unique.', $projectKey));
        }

        if (($this->isProjectExists)('froq_project_number', (string) $project->froqProjectNumber)) {
            $errors[] = new ValidationError(propertyPath: 'Project', message: sprintf('Project %s already exists, this has to be unique.', $projectKey));
        }

        if (!isset($project->froqProjectNumber)) {
            $errors[] = new ValidationError(propertyPath: 'Project', message: sprintf('Project %s has no froqProjectNumber.', $projectKey));
        }

        if (!isset($project->froqName)) {
            $errors[] = new ValidationError(propertyPath: 'Project', message: sprintf('Project %s has no froqName.', $projectKey));
        }

        if (!isset($project->projectName)) {
            $errors[] = new ValidationError(propertyPath: 'Project', message: sprintf('Project %s has no projectName.', $projectKey));
        }

        if (!isset($project->projectCode)) {
            $errors[] = new ValidationError(propertyPath: 'Project', message: sprintf('Project %s has no projectCode.', $projectKey));
        }

        $printerData = (array) json_decode((string) $request->request->get('printerData'), true);
        $printer = new PrinterFromPayload(
            printingProcess: $printerData['printingProcess'] ?? null,
            printingWorkflow: $printerData['printingWorkflow'] ?? null,
            epsonMaterial: $printerData['epsonMaterial'] ?? null,
            substrateMaterial: $printerData['substrateMaterial'] ?? null,
        );

        $printerPath = $rootAssetResourceFolder.AssetResourceOrganizationFolderNames::Printers->name.'/';
        $printerKey = $printer->printingProcess;

        if (($this->isPathExists)((string) $printerKey, $printerPath)) {
            $errors[] = new ValidationError(propertyPath: 'Printer', message: sprintf('Printer %s path already exists, this has to be unique.', $printerPath.$printerKey));
        }

        $supplierData = (array) json_decode((string) $request->request->get('supplierData'), true);
        $supplier = new SupplierFromPayload(
            supplierCode: $supplierData['supplierCode'] ?? null,
            supplierCompany: $supplierData['supplierCompany'] ?? null,
            supplierContact: $supplierData['supplierContact'] ?? null,
            supplierStreetName: $supplierData['supplierStreetName'] ?? null,
            supplierStreetNumber: $supplierData['supplierStreetNumber'] ?? null,
            supplierPostalCode: $supplierData['supplierPostalCode'] ?? null,
            supplierPhoneNumber: $supplierData['supplierPhoneNumber'] ?? null,
            supplierEmail: $supplierData['supplierEmail'] ?? null,
        );

        $supplierPath = $rootAssetResourceFolder.AssetResourceOrganizationFolderNames::Suppliers->name.'/';
        $supplierKey = $supplier->supplierCode;

        if (($this->isPathExists)((string) $supplierKey, $supplierPath)) {
            $errors[] = new ValidationError(propertyPath: 'Supplier', message: sprintf('Supplier %s path already exists, this has to be unique.', $supplierPath.$supplierKey));
        }

        try {
            if (!($this->isAssetAndAssetResourceInSync)($organization, (string)$request->request->get('filename'))) {
                $errors[] = new ValidationError(propertyPath: 'Asset', message: 'Asset And AssetResource are not in sync! Please fix it!');
            }
        } catch (Exception|\Doctrine\DBAL\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
