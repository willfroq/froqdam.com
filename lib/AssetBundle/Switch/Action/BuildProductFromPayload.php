<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\ValueObject\CategoryFromPayload;
use Froq\AssetBundle\Switch\ValueObject\ProductFromPayload;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Froq\AssetBundle\Utility\IsPathExists;
use Froq\PortalBundle\Api\ValueObject\ValidationError;
use Froq\PortalBundle\Repository\ProductRepository;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductAttributes;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;

final class BuildProductFromPayload
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull,
        private readonly BuildCategoryFromPayload $buildCategoryFromPayload,
        private readonly BuildProductContentsFromPayload $buildProductContentsFromPayload,
        private readonly IsPathExists $isPathExists,
    ) {
    }

    /**
     * @throws Exception
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
            return;
        }

        $productData = (array) json_decode($switchUploadRequest->productData, true);

        if (empty($productData) || ($this->allPropsEmptyOrNull)($productData)) {
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

        $assetResourceId = current($assetResources) instanceof AssetResource ? current($assetResources)->getId() : '';

        $product = Product::getById($this->productRepository->getRelatedProductId((string) $assetResourceId));

        if (!($product instanceof Product)) {
            $product = new Product();
        }

        $product->setName($productFromPayload->productName);
        $product->setEAN($productFromPayload->productEAN);
        $product->setSKU($productFromPayload->productSKU);
        $product->setName($productFromPayload->productName);

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

        $product->setNetContentStatement($productFromPayload->productNetContentStatement);

        ($this->buildProductContentsFromPayload)($product, $productFromPayload);

        if (isset($productFromPayload->productCategories)) {
            $product->setCategories(($this->buildCategoryFromPayload)($productFromPayload->productCategories, $organization, $product, $switchUploadRequest, $actions));
        }

        $recentAssetResources = [...$product->getAssets(), ...$assetResources];

        $assetResources = array_unique($recentAssetResources);

        $productKey = pathinfo($switchUploadRequest->filename, PATHINFO_FILENAME);
        $productPath = $rootProductFolder.AssetResourceOrganizationFolderNames::Products->name.'/';

        if (!($this->isPathExists)($productKey, $productPath)) {
            $product->setAssets($assetResources);
            $product->setParentId((int) $parentProductFolder->getId());
            $product->setKey(pathinfo($switchUploadRequest->filename, PATHINFO_FILENAME));
            $product->setPublished(true);

            $product->save();

            $existingProducts = $organization->getProducts();

            $products = array_unique([...$existingProducts, $product]);

            $organization->setProducts($products);

            $organization->save();
        }
    }
}
