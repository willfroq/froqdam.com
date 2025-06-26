<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Action;

use Exception;
use Froq\AssetBundle\Pimtoday\Controller\Request\PimtodayUploadRequest;
use Froq\AssetBundle\Pimtoday\Controller\Request\PimtodayUploadResponse;
use Froq\AssetBundle\Switch\Action\BuildFileAsset;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Project;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class CreateAssetResource
{
    public function __construct(
        private readonly BuildFileAsset $buildFileAsset,
        private readonly ApplicationLogger $logger,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(
        Asset\Folder $assetFolder,
        PimtodayUploadRequest $pimtodayUploadRequest,
        string $assetFolderPath,
        string $assetResourceFolderPath,
        Organization $organization,
        UploadedFile $uploadedFile
    ): PimtodayUploadResponse {
        $assetFolderContainer = (new Asset\Listing())
            ->addConditionParam('filename = ?', $pimtodayUploadRequest->documentData?->documentName)
            ->addConditionParam('path = ?', $assetFolderPath)
            ->current();

        if (!($assetFolderContainer instanceof Asset)) {
            $assetFolderContainer = new Asset\Folder();
            $assetFolderContainer->setParent($assetFolder);
            $assetFolderContainer->setFilename((string) $pimtodayUploadRequest->documentData?->documentName);
            $assetFolderContainer->setPath($assetFolderPath);
            $assetFolderContainer->save();
        }

        $newAssetVersionFolder = new Asset\Folder();
        $newAssetVersionFolder->setParent($assetFolderContainer);
        $newAssetVersionFolder->setFilename('1');
        $newAssetVersionFolder->setPath($assetFolderPath."{$pimtodayUploadRequest->documentData?->documentName}/");
        $newAssetVersionFolder->save();

        $asset = ($this->buildFileAsset)($uploadedFile, (string) $pimtodayUploadRequest->documentData?->documentName, $newAssetVersionFolder);

        $assetResourceFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Assets->readable())
            ->addConditionParam('o_path = ?', "$assetResourceFolderPath")
            ->current();

        if (!($assetResourceFolder instanceof DataObject\Folder)) {
            throw new Exception(message: 'Make a /Customers/org-name/Assets/ folder in the admin!');
        }

        $parentAssetResource = new AssetResource();
        $parentAssetResource->setPublished(true);
        $parentAssetResource->setPath($assetResourceFolderPath.AssetResourceOrganizationFolderNames::Assets->readable().'/');
        $parentAssetResource->setName($pimtodayUploadRequest->documentData?->documentName);
        $parentAssetResource->setParentId((int) $assetResourceFolder->getId());
        $parentAssetResource->setAssetVersion(0);
        $parentAssetResource->setKey((string) $pimtodayUploadRequest->documentData?->documentName);

        $parentAssetResource->save();

        $assetResourceVersionOne = new AssetResource();
        $assetResourceVersionOne->setPublished(true);
        $assetResourceVersionOne->setPath($parentAssetResource->getPath().$parentAssetResource->getKey().'/');
        $assetResourceVersionOne->setName($pimtodayUploadRequest->documentData?->documentName);
        $assetResourceVersionOne->setParentId((int) $parentAssetResource->getId());
        $assetResourceVersionOne->setAsset($asset);
        $assetResourceVersionOne->setAssetVersion(1);
        $assetResourceVersionOne->setKey('1');
        $assetResourceVersionOne->setPimTodayId((int) $pimtodayUploadRequest->documentData?->pimTodayId);

        $assetResourceVersionOne->save();

        $product = null;

        if (!empty($pimtodayUploadRequest->productData?->pimTodayEan)) {
            $product = (new Product\Listing())
                ->addConditionParam('EAN = ?', $pimtodayUploadRequest->productData->pimTodayEan)
                ->addConditionParam('o_path = ?', $assetResourceFolderPath . AssetResourceOrganizationFolderNames::Products->readable() . '/')
                ->current();
        }

        if (!empty($pimtodayUploadRequest->productData?->pimTodaySku) && empty($pimtodayUploadRequest->productData?->pimTodayEan)) {
            $product = (new Product\Listing())
                ->addConditionParam('SKU = ?', $pimtodayUploadRequest->productData->pimTodaySku)
                ->addConditionParam('o_path = ?', $assetResourceFolderPath . AssetResourceOrganizationFolderNames::Products->readable() . '/')
                ->current();
        }

        if ($product instanceof Product) {
            $existingProductAssetResources = $product->getAssets();
            $product->setAssets([...$existingProductAssetResources, $parentAssetResource]);

            $product->setAssets([$parentAssetResource]);
            $product->setPimTodayId($pimtodayUploadRequest->productData?->pimTodayId);

            $product->save();
        }

        if (!($product instanceof Product)) {
            $parentProductFolder = (new DataObject\Listing())
                ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Products->readable())
                ->addConditionParam('o_path = ?', $assetResourceFolderPath)
                ->current();

            if (!($parentProductFolder instanceof DataObject\Folder)) {
                $message = "$parentProductFolder parentProductFolder does not exist, make this folder in the admin!";

                $this->logger->error(message: $message, context: ['component' => 'pimtoday_upload']);

                throw new Exception(message: $message);
            }

            $productKey = null;

            if (!empty($pimtodayUploadRequest->productData->pimTodayEan)) {
                $productKey = $pimtodayUploadRequest->productData->pimTodayEan;
            }

            if (!empty($pimtodayUploadRequest->productData->pimTodaySku) && empty($productKey)) {
                $productKey = $pimtodayUploadRequest->productData->pimTodaySku;
            }

            if (!empty($pimtodayUploadRequest->productData->pimTodaySku) || !empty($pimtodayUploadRequest->productData->pimTodayEan)) {
                $product = new Product();

                $product->setName($productKey);
                $product->setKey($productKey ?? 'product-'.date('y-d-m-Y-H-i-s'));
                $product->setEAN($pimtodayUploadRequest->productData->pimTodayEan);
                $product->setSKU($pimtodayUploadRequest->productData->pimTodaySku);
                $product->setAssets([$parentAssetResource]);
                $product->setParentId((int) $parentProductFolder->getId());
                $product->setPublished(true);
                $product->setPimTodayId($pimtodayUploadRequest->productData->pimTodayId);

                $product->save();
            }
        }

        $parentProjectFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Projects->readable())
            ->addConditionParam('o_path = ?', $assetResourceFolderPath)
            ->current();

        if (!($parentProjectFolder instanceof DataObject\Folder)) {
            $message = "$parentProjectFolder parentProjectFolder does not exist, make this folder in the admin!";

            $this->logger->error(message: $message, context: ['component' => 'pimtoday_upload']);

            throw new Exception(message: $message);
        }

        $project = null;

        if (!empty($pimtodayUploadRequest->projectData->froqProjectNumber)) {
            $project = (new Project\Listing())
                ->addConditionParam('o_key = ?', $pimtodayUploadRequest->projectData->froqProjectNumber)
                ->addConditionParam('o_path = ?', $assetResourceFolderPath . AssetResourceOrganizationFolderNames::Projects->readable() . '/')
                ->current();
        }

        if ($project instanceof Project) {
            $existingProjectAssetResources = $project->getAssets();
            $project->setAssets([...$existingProjectAssetResources, $parentAssetResource]);

            $project->setCode((string) $pimtodayUploadRequest->projectData?->froqProjectNumber);
            $project->setPim_project_number((string) $pimtodayUploadRequest->projectData?->projectNumber);
            $project->setFroq_name((string) $pimtodayUploadRequest->projectData?->projectName);
            $project->setName($pimtodayUploadRequest->projectData?->projectName);
            $project->setFroq_project_number($pimtodayUploadRequest->projectData?->froqProjectNumber);
            $project->setDescription($pimtodayUploadRequest->projectData?->description);
            $project->setProject_type((string) $pimtodayUploadRequest->projectData?->projectType);
            $project->setStatus($pimtodayUploadRequest->projectData?->status);
            $project->setLocation($pimtodayUploadRequest->projectData?->location);
            $project->setParentId((int) $parentProjectFolder->getId());
            $project->setPublished(true);
            $project->setPimTodayId($pimtodayUploadRequest->projectData?->pimTodayId);

            $project->save();
        }

        if (!($project instanceof Project)) {
            $project = new Project();

            $key = is_null($pimtodayUploadRequest->projectData?->projectNumber) ? $pimtodayUploadRequest->projectData?->projectNumber : 'project-'.date('y-d-m-Y-H-i-s');

            $project->setAssets([$parentAssetResource]);
            $project->setCode((string) $pimtodayUploadRequest->projectData?->froqProjectNumber);
            $project->setPim_project_number((string) $pimtodayUploadRequest->projectData?->projectNumber);
            $project->setFroq_name((string) $pimtodayUploadRequest->projectData?->projectName);
            $project->setName($pimtodayUploadRequest->projectData?->projectName);
            $project->setKey((string) $key);
            $project->setFroq_project_number($pimtodayUploadRequest->projectData?->froqProjectNumber);
            $project->setDescription($pimtodayUploadRequest->projectData?->description);
            $project->setProject_type((string) $pimtodayUploadRequest->projectData?->projectType);
            $project->setStatus($pimtodayUploadRequest->projectData?->status);
            $project->setLocation($pimtodayUploadRequest->projectData?->location);
            $project->setCustomer($organization);
            $project->setParentId((int) $parentProjectFolder->getId());
            $project->setPublished(true);
            $project->setPimTodayId($pimtodayUploadRequest->projectData?->pimTodayId);

            $project->save();
        }

        $existingAssetResources = $organization->getAssetResources();

        $assetResources = array_values(array_unique([...$existingAssetResources, $parentAssetResource]));

        $organization->setAssetResources($assetResources);

        $organization->save();

        return new PimtodayUploadResponse(
            eventName: $pimtodayUploadRequest->eventName,
            date: date('F j, Y H:i'),
            filename: $pimtodayUploadRequest->documentData?->documentName,
            pimtodayProjectId: $pimtodayUploadRequest->projectData?->pimTodayId,
            damProjectId: $project instanceof Project ? $project->getId() : null, /** @phpstan-ignore-line */
            pimtodaySkuId: $pimtodayUploadRequest->productData?->pimTodayId,
            damSkuId: $product instanceof Product ? $product->getId() : null,
            pimtodayDocumentId: $pimtodayUploadRequest->documentData?->pimTodayId,
            damAssetResourceId: (int) $assetResourceVersionOne->getId(),
            version: (int) $assetResourceVersionOne->getKey(),
            statusCode: 200,
            errors: [],
        );
    }
}
