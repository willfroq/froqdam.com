<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\Action;

use Froq\AssetBundle\Export\DataTransferObject\AssetDto;
use Froq\AssetBundle\Export\DataTransferObject\AssetResourceExportCollection;
use Froq\AssetBundle\Export\DataTransferObject\AssetResourceExportItem;
use Froq\AssetBundle\Export\DataTransferObject\CategoryExportItem;
use Froq\AssetBundle\Export\DataTransferObject\ProductContentDto;
use Froq\AssetBundle\Export\DataTransferObject\ProductExportItem;
use Froq\AssetBundle\Export\DataTransferObject\ProjectExportItem;
use Froq\AssetBundle\Export\DataTransferObject\TagExportItem;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Category;
use Pimcore\Model\DataObject\Fieldcollection\Data\AssetResourceMetadata;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductAttributes;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductContents;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Project;
use Pimcore\Model\DataObject\Tag;
use Symfony\Component\HttpFoundation\Request;

final class BuildExportAssetResourceCollection
{
    public function __invoke(Request $request, Organization $organization): AssetResourceExportCollection
    {
        $offset = is_numeric($request->query->get('offset')) ? (int) $request->query->get('offset') : 1;
        $limit = is_numeric($request->query->get('limit')) ? (int) $request->query->get('limit') : 5;

        $parentAssetResourcesListing = (new AssetResource\Listing())
            ->setCondition(
                '`o_path` = ? OR `o_path` = ? OR `o_path` = ? OR `o_path` = ? OR `o_path` = ?', [
                    "/Customers/{$organization->getKey()}/".AssetResourceOrganizationFolderNames::Assets->readable().'/',
                    "/Customers/{$organization->getKey()}/".AssetResourceOrganizationFolderNames::ThreeDModelLibrary->readable().'/',
                    "/Customers/{$organization->getKey()}/".AssetResourceOrganizationFolderNames::Cutter_Guides->readable().'/',
                    "/Customers/{$organization->getKey()}/".AssetResourceOrganizationFolderNames::Mockups->readable().'/',
                    "/Customers/{$organization->getKey()}/".AssetResourceOrganizationFolderNames::Packshots->readable().'/',
                ]
            );

        $parentAssetResources = $parentAssetResourcesListing
            ->setOffset($offset)
            ->setLimit($limit)
            ->setOrderKey('Name')
            ->setOrder('asc');

        $parentAssetResourceItems = [];

        foreach ($parentAssetResources as $parentAssetResource) {
            if (!($parentAssetResource instanceof AssetResource)) {
                continue;
            }

            $children = [];

            foreach ($parentAssetResource->getChildren() as $childAssetResource) {
                if (!($childAssetResource instanceof AssetResource)) {
                    continue;
                }

                $asset = $childAssetResource->getAsset();

                if (!($asset instanceof Asset)) {
                    continue;
                }

                $children[] = $this->createAssetResourceItem($childAssetResource, [], $this->createAssetDto($asset));
            }

            $parentAssetResourceItems[] = $this->createAssetResourceItem($parentAssetResource, $children, null);
        }

        return new AssetResourceExportCollection(
            organizationId: (int) $organization->getId(),
            offset: $offset,
            limit: $limit,
            totalCount: $parentAssetResourcesListing->count(),
            parentAssetResourceExportItems: $parentAssetResourceItems
        );
    }

    /** @param array<int, AssetResourceExportItem> $children */
    private function createAssetResourceItem(AssetResource $assetResource, array $children, ?AssetDto $assetDto): AssetResourceExportItem
    {
        $parts = array_filter(explode('/', $assetResource->getFullPath()));
        $assetFolderName = $parts[3];

        return new AssetResourceExportItem(
            id: (int) $assetResource->getId(),
            name: (string) $assetResource->getName(),
            key: (string) $assetResource->getKey(),
            path: (string) $assetResource->getPath(),
            assetFolderName: (string) $assetFolderName,
            assetDto: $assetDto,
            children: $children,
            validFrom: (int) $assetResource->getValidFrom()?->timestamp,
            validUntil: (int) $assetResource->getValidUntil()?->timestamp,
            fileCreateDate: (int) $assetResource->getFileCreateDate()?->timestamp,
            fileModifyDate: (int) $assetResource->getFileModifyDate()?->timestamp,
            assetTypeId: (int) $assetResource->getAssetType()?->getId(),
            assetTypePath: (string) $assetResource->getAssetType()?->getFullPath(),
            assetTypeKey: (string) $assetResource->getAssetType()?->getKey(),
            uploadName: (string) $assetResource->getUploadName(),
            assetVersion: (int) $assetResource->getAssetVersion(),
            highestVersionNumber: $assetResource->getHighestVersionNumber(),
            metadata: $this->createAssetResourceMetadata($assetResource),
            pdfText: (string) $assetResource->getPdfText(),
            embeddedMetadata: (string) $assetResource->getEmbeddedMetadata(),
            exifData: (string) $assetResource->getExifData(),
            xmpData: (string) $assetResource->getXmpData(),
            iptcData: (string) $assetResource->getIptcData(),
            contains: [],
            usedIn: $this->createUsedInItems($assetResource),
            projects: $this->createProjectItems($assetResource),
            products: $this->createProductItems($assetResource),
            tags: $this->createTagItems($assetResource)
        );
    }

    private function createAssetDto(Asset $asset): AssetDto
    {
        return new AssetDto(
            id: (int) $asset->getId(),
            filename: (string) $asset->getFilename(),
            path: (string) $asset->getPath(),
            fullPath: $asset->getFullPath(),
        );
    }

    /** @return array<int, AssetResourceExportItem> */
    private function createUsedInItems(AssetResource $assetResource): array
    {
        $usedIns = [];

        foreach ($assetResource->getUsedIn() as $assetResource) {
            if (!($assetResource instanceof AssetResource)) {
                continue;
            }

            $usedIns[] = $this->createAssetResourceItem($assetResource, [], null);
        }

        return $usedIns;
    }

    /** @return array<int, ProjectExportItem> */
    private function createProjectItems(AssetResource $assetResource): array
    {
        $projects = [];

        foreach ($assetResource->getProjects() as $project) {
            if (!($project instanceof Project)) {
                continue;
            }

            $projects[] = new ProjectExportItem(
                id: (int) $project->getId(),
                key: (string) $project->getKey(),
                path: (string) $project->getPath(),
                code: (string) $project->getCode(),
                name: (string) $project->getName(),
                froqName: (string) $project->getFroq_name(),
                pimProjectNumber: (string) $project->getPim_project_number(),
                froqProjectNumber: (string) $project->getFroq_project_number(),
                customerProjectNumber: (string) $project->getCustomer_project_number2(),
                description: (string) $project->getDescription(),
            );
        }

        return $projects;
    }

    /** @return array<int, ProductExportItem> */
    private function createProductItems(AssetResource $assetResource): array
    {
        $products = [];

        foreach ($assetResource->getProducts() as $product) {
            if (!($product instanceof Product)) {
                continue;
            }

            $products[] = new ProductExportItem(
                id: (int) $product->getId(),
                key: (string) $product->getKey(),
                path: (string) $product->getPath(),
                name: (string) $product->getName(),
                sku: (string) $product->getSKU(),
                ean: (string) $product->getEAN(),
                attributes: $this->createAttributes($product),
                netContentStatement: (string) $product->getNetContentStatement(),
                netContents: $this->createNetContents($product),
                netUnitContents: $this->createNetUnitContents($product),
                categories: $this->createCategoryItems($product)
            );
        }

        return $products;
    }

    /** @return array<int, TagExportItem> */
    private function createTagItems(AssetResource $assetResource): array
    {
        $tags = [];

        foreach ($assetResource->getTags() as $tag) {
            if (!($tag instanceof Tag)) {
                continue;
            }

            $tags[] = new TagExportItem(
                id: (int) $tag->getId(),
                key: (string) $tag->getKey(),
                path: (string) $tag->getPath(),
                code: (string) $tag->getCode(),
                name: (string) $tag->getName()
            );
        }

        return $tags;
    }

    /** @return array<int, CategoryExportItem> */
    private function createCategoryItems(Product $product): array
    {
        $categories = [];

        foreach ($product->getCategories() as $category) {
            if (!($category instanceof Category)) {
                continue;
            }

            $categories[] = new CategoryExportItem(
                id: (int) $category->getId(),
                key: (string) $category->getKey(),
                path: (string) $category->getPath(),
                reportingType: (string) $category->getReportingType(),
                levelLabel: (string) $category->getLevelLabel()
            );
        }

        return $categories;
    }

    /** @return array<string, string> */
    private function createAssetResourceMetadata(AssetResource $childAssetResource): array
    {
        $assetResourceMetadata = [];

        foreach ($childAssetResource->getMetadata()?->getItems() ?? [] as $metadata) {
            if (!($metadata instanceof AssetResourceMetadata)) {
                continue;
            }

            $assetResourceMetadata[(string) $metadata->getMetadataKey()] = (string) $metadata->getMetadataValue();
        }

        return $assetResourceMetadata;
    }

    /** @return array<string, string> */
    private function createAttributes(Product $product): array
    {
        $attributes = [];

        foreach ($product->getAttributes()?->getItems() ?? [] as $attribute) {
            if (!($attribute instanceof ProductAttributes)) {
                continue;
            }

            $attributes[(string) $attribute->getAttributeKey()] = (string) $attribute->getAttributeValue();
        }

        return $attributes;
    }

    /** @return array<int, ProductContentDto> */
    private function createNetContents(Product $product): array
    {
        $netContents = [];

        foreach ($product->getNetContents() ?? [] as $productContents) {
            if (!($productContents instanceof ProductContents)) {
                continue;
            }

            $netContents[] = new ProductContentDto(
                value: (string) $productContents->getNetContent()?->getValue(),
                unitId: (string) $productContents->getNetContent()?->getUnitId(),
            );
        }

        return $netContents;
    }

    /** @return array<int, ProductContentDto> */
    private function createNetUnitContents(Product $product): array
    {
        $netUnitContents = [];

        foreach ($product->getNetUnitContents() ?? [] as $productContents) {
            if (!($productContents instanceof ProductContents)) {
                continue;
            }

            $netUnitContents[] = new ProductContentDto(
                value: (string) $productContents->getNetContent()?->getValue(),
                unitId: (string) $productContents->getNetContent()?->getUnitId(),
            );
        }

        return $netUnitContents;
    }
}
