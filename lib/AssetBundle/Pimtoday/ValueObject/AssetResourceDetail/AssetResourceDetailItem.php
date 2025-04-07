<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail;

use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection\AssetInfoSectionItem;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection\PrinterInfoSectionCollection;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection\ProductInfoSectionCollection;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection\ProjectInfoSectionCollection;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection\SupplierInfoSectionCollection;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\TabSection\LinkedCollection;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\TabSection\RelatedCollection;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\TabSection\VersionCollection;
use Webmozart\Assert\Assert;

final class AssetResourceDetailItem
{
    public function __construct(
        public int $id,
        public string $filename,
        public string $filePreviewPath,
        public bool $hasErrors,
        // sidebar
        public ?AssetInfoSectionItem $assetInfoSectionItem,
        public ?ProductInfoSectionCollection $productInfoSectionCollection,
        public ?ProjectInfoSectionCollection $projectInfoSectionCollection,
        public ?SupplierInfoSectionCollection $supplierInfoSectionCollection,
        public ?PrinterInfoSectionCollection $printerInfoSectionCollection,
        // tabs
        public ?VersionCollection $versionCollection,
        public ?RelatedCollection $relatedCollection,
        public ?LinkedCollection $linkedCollection,
    ) {
        Assert::numeric($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::string($this->filename, 'Expected "thumbnailLink" to be a string, got %s');
        Assert::string($this->filePreviewPath, 'Expected "filePreviewPath" to be a string, got %s');
        Assert::boolean($this->hasErrors, 'Expected "hasErrors" to be a bool, got %s');

        Assert::nullOrIsInstanceOf($this->assetInfoSectionItem, AssetInfoSectionItem::class, 'Expected "assetInfoSectionItem" to be instance of AssetInfoSectionItem, got %s');
        Assert::nullOrIsInstanceOf($this->productInfoSectionCollection, ProductInfoSectionCollection::class, 'Expected "productInfoSectionCollection" to be instance of ProductInfoSectionCollection, got %s');
        Assert::nullOrIsInstanceOf($this->projectInfoSectionCollection, ProjectInfoSectionCollection::class, 'Expected "projectInfoSectionCollection" to be instance of ProjectInfoSectionCollection, got %s');
        Assert::nullOrIsInstanceOf($this->supplierInfoSectionCollection, SupplierInfoSectionCollection::class, 'Expected "supplierInfoSectionCollection" to be instance of SupplierInfoSectionCollection, got %s');
        Assert::nullOrIsInstanceOf($this->printerInfoSectionCollection, PrinterInfoSectionCollection::class, 'Expected "printerInfoSectionCollection" to be instance of PrinterInfoSectionCollection, got %s');

        Assert::nullOrIsInstanceOf($this->versionCollection, VersionCollection::class, 'Expected "versionCollection" to be instance of VersionCollection, got %s');
        Assert::nullOrIsInstanceOf($this->relatedCollection, RelatedCollection::class, 'Expected "relatedCollection" to be instance of RelatedCollection, got %s');
        Assert::nullOrIsInstanceOf($this->linkedCollection, LinkedCollection::class, 'Expected "linkedCollection" to be instance of LinkedCollection, got %s');
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'filePreviewPath' => $this->filePreviewPath,

            'assetInfoSectionItem' => $this->assetInfoSectionItem,
            'productInfoSectionCollection' => $this->productInfoSectionCollection,
            'projectInfoSectionCollection' => $this->projectInfoSectionCollection,
            'supplierInfoSectionCollection' => $this->supplierInfoSectionCollection,
            'printerInfoSectionCollection' => $this->printerInfoSectionCollection,

            'versionCollection' => $this->versionCollection,
            'relatedCollection' => $this->relatedCollection,
            'linkedCollection' => $this->linkedCollection,
        ];
    }
}
