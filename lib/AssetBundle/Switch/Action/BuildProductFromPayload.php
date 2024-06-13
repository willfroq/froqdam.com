<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\Action\Processor\ProcessProduct;
use Froq\AssetBundle\Switch\Action\RelatedObject\CreateProductFolder;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\ValueObject\CategoryFromPayload;
use Froq\AssetBundle\Switch\ValueObject\ProductFromPayload;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Froq\AssetBundle\Utility\IsPathExists;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductAttributes;
use Pimcore\Model\DataObject\Organization;

final class BuildProductFromPayload
{
    public function __construct(
        private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull,
        private readonly BuildCategoryFromPayload $buildCategoryFromPayload,
        private readonly BuildProductContentsFromPayload $buildProductContentsFromPayload,
        private readonly IsPathExists $isPathExists,
        private readonly CreateProductFolder $createProductFolder,
        private readonly ProcessProduct $processProduct,
    ) {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     *
     * @param array<int, string> $actions
     * @param array<int, AssetResource> $assetResources
     */
    public function __invoke(
        SwitchUploadRequest $switchUploadRequest,
        array $assetResources,
        Organization $organization,
        array $actions
    ): void {
        $rootProductFolder = $organization->getObjectFolder() . '/';

        $parentProductFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Products->name)
            ->addConditionParam('o_path = ?', $rootProductFolder)
            ->current();

        if (!($parentProductFolder instanceof DataObject)) {
            $parentProductFolder = ($this->createProductFolder)($organization, $rootProductFolder);
        }

        $productData = (array) json_decode($switchUploadRequest->productData, true);

        if (!isset($productData['productName'])) {
            return;
        }

        if (($this->allPropsEmptyOrNull)($productData)) {
            return;
        }

        $categories = $productData['productCategories'] ?? null;

        $productFromPayload = new ProductFromPayload(
            productName: $productData['productName'] ?? null,
            productEAN: $productData['productEAN'] ?? null,
            productSKU: $productData['productSKU'] ?? null,
            productAttributes: $productData['productAttributes'] ?? null,
            productNetContentStatement: $productData['productNetContentStatement'] ?? null,
            productNetContents: $productData['productNetContents'] ?? null,
            productNetUnitContents: $productData['productNetUnitContents'] ?? null,
            productCategories: new CategoryFromPayload(
                brand: $categories['brand'] ?? null,
                campaign: $categories['campaign'] ?? null,
                market: $categories['market'] ?? null,
                segment: $categories['segment'] ?? null,
                platform: $categories['platform'] ?? null,
            )
        );

        $parentAssetResource = current($assetResources);

        if (!($parentAssetResource instanceof AssetResource)) {
            throw new \Exception(message: 'No container folder i.e. /Customers/org-name/Assets/filename');
        }

        $product = ($this->processProduct)($organization, $productFromPayload);

        if ($product->getName() === null) {
            $product->setName($productFromPayload->productName);
        }

        if ($product->getEAN() === null) {
            $product->setEAN($productFromPayload->productEAN);
        }

        if ($product->getSKU() === null) {
            $product->setSKU($productFromPayload->productSKU);
        }

        if (isset($productFromPayload->productAttributes) && is_array($productFromPayload->productAttributes)) {
            $fieldCollectionItems = [];

            foreach ($productFromPayload->productAttributes as $item) {
                if (empty($item)) {
                    continue;
                }

                $productAttributes = new ProductAttributes();

                $productAttributes->setAttributeKey((string) array_key_first($item));
                $productAttributes->setAttributeValue(current($item));

                $fieldCollectionItems[] = $productAttributes;
            }

            $productAttributesFieldCollection = new Fieldcollection();
            $productAttributesFieldCollection->setItems($fieldCollectionItems);

            $product->setAttributes($productAttributesFieldCollection);
        }

        if ($product->getNetContentStatement() === null) {
            $product->setNetContentStatement($productFromPayload->productNetContentStatement);
        }

        ($this->buildProductContentsFromPayload)($product, $productFromPayload);

        if (isset($productFromPayload->productCategories)) {
            $product->setCategories(($this->buildCategoryFromPayload)($productFromPayload->productCategories, $organization, $product, $switchUploadRequest, $actions));
        }

        $recentAssetResources = [...$product->getAssets(), ...$assetResources];

        $assetResources = array_values(array_unique($recentAssetResources));

        $productKey = (string) $productFromPayload->productName;
        $productPath = $rootProductFolder.AssetResourceOrganizationFolderNames::Products->readable().'/';

        if (!($this->isPathExists)($productKey, $productPath)) {
            $product->setAssets($assetResources);
            $product->setParentId((int) $parentProductFolder->getId());
            $product->setKey($productKey);
            $product->setPublished(true);
        }

        $product->save();
    }
}
