<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Action\Search\Builder;

use Froq\AssetBundle\Pimtoday\Enum\ThumbnailTypes;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection\AssetInfoSectionItem;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection\AssetResourceMetadataItem;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection\CategoryItem;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection\PrinterInfoSectionCollection;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection\ProductInfoSectionCollection;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection\ProductInfoSectionItem;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection\ProductNetContent;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection\ProductNetUnitContent;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection\ProjectInfoSectionCollection;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection\ProjectInfoSectionItem;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection\SupplierInfoSectionCollection;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection\TagItem;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetResourceDetailItem;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\TabSection\LinkedCollection;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\TabSection\LinkedItem;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\TabSection\RelatedCollection;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\TabSection\RelatedItem;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\TabSection\VersionCollection;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\TabSection\VersionItem;
use Froq\PortalBundle\Twig\AssetPreviewExtension;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Category;
use Pimcore\Model\DataObject\Fieldcollection\Data\AssetResourceMetadata;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductContents;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Project;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class BuildDetailResponse
{
    public function __construct(private readonly AssetPreviewExtension $assetPreviewExtension, private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request, AssetResource $assetResource): AssetResourceDetailItem
    {
        $hasErrors = false;

        $parentAssetResource = $assetResource->getParent();

        if (!($parentAssetResource instanceof AssetResource)) {
            $hasErrors = true;
        }

        $domain = $request->getSchemeAndHttpHost();

        $asset = $assetResource->getAsset();

        if (!($asset instanceof Asset)) {
            $hasErrors = true;
        }

        $filePreviewPath = match ($asset?->getType()) {
            'document' => (
                fn () => $asset instanceof Asset\Document ? $this->assetPreviewExtension->getDocumentPreviewURL($asset) : ''
            )(),
            'image' => (
                fn () => $asset instanceof Asset\Image ? $this->assetPreviewExtension->getImagePreviewURL($asset) : ''
            )(),
            'text' => (
                fn () => $asset instanceof Asset\Text ? $this->assetPreviewExtension->getTextPreviewContent($asset) : ''
            )(),
            'unknown' => $this->assetPreviewExtension->getAssetExtension($asset),

            default => $hasErrors = true
        };

        $tags = [];

        foreach ($assetResource->getTags() as $id => $tag) {
            $tags[] = new TagItem(name: (string) $tag->getCode(), link: "filters[tags][$id]={$tag->getCode()}");
        }

        $assetResourceMetadata = [];

        foreach ($assetResource->getMetadata() ?? [] as $id => $metadata) {
            if (!($metadata instanceof AssetResourceMetadata)) {
                continue;
            }

            $assetResourceMetadata[] = new AssetResourceMetadataItem(
                key: (string) $metadata->getMetadataKey(),
                value: (string) $metadata->getMetadataValue(),
                link: "filters[{$metadata->getMetadataKey()}][$id]={$metadata->getMetadataValue()}"
            );
        }

        if (!($parentAssetResource instanceof AssetResource)) {
            throw new \Exception(message: "$parentAssetResource must be an instance of AssetResource");
        }

        $products = [];

        foreach ($parentAssetResource->getProducts() as $product) {
            if (!($product instanceof Product)) {
                continue;
            }

            $netContents = [];

            foreach ($product->getNetContents() ?? [] as $id => $netContent) {
                if (!($netContent instanceof ProductContents)) {
                    continue;
                }

                $unit = (string) $netContent->getNetContent()?->getUnitId();
                $value = (string) $netContent->getNetContent()?->getValue();

                $netContents[] = new ProductNetContent(
                    attribute: $unit,
                    value: $value,
                    link: "filters[net_contents_$unit][$id]=$value"
                );
            }

            $netUnitContents = [];

            foreach ($product->getNetUnitContents() ?? [] as $id => $netUnitContent) {
                if (!($netUnitContent instanceof ProductContents)) {
                    continue;
                }

                $unit = (string) $netUnitContent->getNetContent()?->getUnitId();
                $value = (string) $netUnitContent->getNetContent()?->getValue();

                $netUnitContents[] = new ProductNetUnitContent(
                    attribute: $unit,
                    value: $value,
                    link: "filters[net_contents_$unit][$id]=$value"
                );
            }

            $brands = [];

            foreach ($product->getCategories() as $id =>  $category) {
                if (!($category instanceof Category)) {
                    continue;
                }

                $name = (string) $category->getLevelLabel();
                $value = preg_replace('/s$/', '', $name);

                $brands[] = new CategoryItem(
                    name: $name,
                    value: (string) $value,
                    link: "filters[product_category_$value][$id]=$value"
                );
            }

            $products[] = new ProductInfoSectionItem(
                name: (string) $product->getName(),
                sku: (string) $product->getSKU(),
                ean: (string) $product->getEAN(),
                contents: (string) $product->getNetContentStatement(),
                netContents: $netContents,
                netUnitContents: $netUnitContents,
                brands: $brands
            );
        }

        $projects = [];

        foreach ($parentAssetResource->getProjects() as $project) {
            if (!($project instanceof Project)) {
                continue;
            }

            $projects[] = new ProjectInfoSectionItem(
                pimProjectName: (string) $project->getName(),
                froqName: (string) $project->getFroq_name(),
                froqNumber: (string) $project->getFroq_project_number()
            );
        }

        $versions = [];

        foreach ($parentAssetResource->getChildren() as $child) {
            if (!($child instanceof AssetResource)) {
                continue;
            }

            $asset = $child->getAsset();

            if (!($asset instanceof Asset)) {
                continue;
            }

            $id = (int) $child->getId();

            $versions[] = new VersionItem(
                id: $id,
                thumbnail: $domain.$this->assetPreviewExtension->getAssetThumbnailHashedURL($asset, ThumbnailTypes::List->value),
                filename: (string) $asset->getFilename(),
                modificationDate: date('l jS \o\f F Y h:i:s A', $child->getFileModifyDate()?->getTimestamp()),
                version: (string) $child->getKey(),
                downloadLink: $this->urlGenerator->generate(
                    'froq_portal.asset_library.detail.download.file',
                    ['id' => $id],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            );
        }

        $relatedInProducts = [];

        foreach ($parentAssetResource->getProducts() as $product) {
            if (!($product instanceof Product)) {
                continue;
            }

            foreach ($product->getAssets() as $assetResource) {
                if (!($assetResource instanceof AssetResource)) {
                    continue;
                }

                $relatedInProducts[] = $assetResource;
            }
        }

        $relatedInProjects = [];

        foreach ($parentAssetResource->getProjects() as $project) {
            if (!($project instanceof Project)) {
                continue;
            }

            foreach ($project->getAssets() as $assetResource) {
                if (!($assetResource instanceof AssetResource)) {
                    continue;
                }

                $relatedInProjects[] = $assetResource;
            }
        }

        /** @var AssetResource[] $relatedItems */
        $relatedItems = [...$relatedInProducts, ...$relatedInProjects];

        $relateds = [];

        foreach ($relatedItems as $relatedAssetResource) {
            if (is_array($relatedAssetResource)) {
                continue;
            }

            if (!($relatedAssetResource instanceof AssetResource)) {
                continue;
            }

            $children = $relatedAssetResource->getChildren();

            $latestAssetResource = end($children);

            if (!($latestAssetResource instanceof AssetResource)) {
                continue;
            }

            $asset = $latestAssetResource->getAsset();

            if (!($asset instanceof Asset)) {
                continue;
            }

            $id = (int) $relatedAssetResource->getId();

            $initialProduct = current($relatedAssetResource->getProducts());

            $product = $initialProduct instanceof Product ? $initialProduct : null;

            $relateds[] = new RelatedItem(
                id: $id,
                thumbnail: $domain.$this->assetPreviewExtension->getAssetThumbnailHashedURL($asset, ThumbnailTypes::List->value),
                filename: (string) $asset->getFilename(),
                productSku: (string) $product?->getSKU(),
                assetTypeName: (string) $relatedAssetResource->getAssetType()?->getName(),
                projectName: date('l jS \o\f F Y h:i:s A', $relatedAssetResource->getFileModifyDate()?->getTimestamp()),
                downloadLink: $this->urlGenerator->generate(
                    'froq_portal.asset_library.detail.download.file',
                    ['id' => $id],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            );
        }

        $linkedAssetResources = [];

        foreach ($parentAssetResource->getContains() as $contain) {
            if (!($contain instanceof AssetResource)) {
                continue;
            }

            $children = $contain->getChildren();

            $latestAssetResource = end($children);

            if (!($latestAssetResource instanceof AssetResource)) {
                continue;
            }

            $asset = $latestAssetResource->getAsset();

            if (!($asset instanceof Asset)) {
                continue;
            }

            $id = (int) $contain->getId();

            $initialProduct = current($contain->getProducts());

            $product = $initialProduct instanceof Product ? $initialProduct : null;

            $linkedAssetResources[] = new LinkedItem(
                id: $id,
                thumbnail: $domain.$this->assetPreviewExtension->getAssetThumbnailHashedURL($asset, ThumbnailTypes::List->value),
                filename: (string) $asset->getFilename(),
                productSku: (string) $product?->getSKU(),
                assetTypeName: (string) $contain->getAssetType()?->getName(),
                projectName: date('l jS \o\f F Y h:i:s A', $contain->getFileModifyDate()?->getTimestamp()),
                downloadLink: $this->urlGenerator->generate(
                    'froq_portal.asset_library.detail.download.file',
                    ['id' => $id],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            );
        }

        return new AssetResourceDetailItem(
            id: (int) $assetResource->getId(),
            filename: (string) $asset?->getFilename(),
            filePreviewPath: (string) $filePreviewPath,
            hasErrors: $hasErrors,
            assetInfoSectionItem: new AssetInfoSectionItem(
                type: (string) $assetResource->getAssetType()?->getName(),
                createdDate: date('l jS \o\f F Y h:i:s A', $assetResource->getCreationDate()),
                fileCreatedDate: date('l jS \o\f F Y h:i:s A', $assetResource->getFileCreateDate()?->getTimestamp()),
                version: (int) $assetResource->getKey(),
                tags: $tags,
                assetResourceMetadata: $assetResourceMetadata
            ),
            productInfoSectionCollection: new ProductInfoSectionCollection(
                totalCount: count($products),
                items: $products
            ),
            projectInfoSectionCollection: new ProjectInfoSectionCollection(
                totalCount: count($projects),
                items: $projects
            ),

            // TODO Make supplier schema later and relate it to AssetResource
            supplierInfoSectionCollection: new SupplierInfoSectionCollection(
                totalCount: count($projects),
                items: []
            ),

            // TODO Make printer schema later and relate it to AssetResource
            printerInfoSectionCollection: new PrinterInfoSectionCollection(
                totalCount: 0,
                items: []
            ),

            versionCollection: new VersionCollection(
                totalCount: count($versions),
                items: $versions
            ),

            relatedCollection: new RelatedCollection(
                totalCount: count($relateds),
                items: $relateds
            ),

            linkedCollection: new LinkedCollection(
                totalCount: count($linkedAssetResources),
                items: $linkedAssetResources
            ),
        );
    }
}
