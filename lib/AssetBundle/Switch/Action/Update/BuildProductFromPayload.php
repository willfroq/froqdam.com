<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\Update;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Action\BuildProductContentsFromPayload;
use Froq\AssetBundle\Switch\Action\RelatedObject\CreateCategoryFolder;
use Froq\AssetBundle\Switch\Action\RelatedObject\CreateCategoryFolderLevelLabel;
use Froq\AssetBundle\Switch\Action\RelatedObject\CreateProductFolder;
use Froq\AssetBundle\Switch\Controller\Request\UpdateRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\Enum\CategoryNames;
use Froq\AssetBundle\Switch\ValueObject\CategoryFromPayload;
use Froq\AssetBundle\Switch\ValueObject\ProductFromPayload;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Category;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductAttributes;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;

final class BuildProductFromPayload
{
    public function __construct(
        private readonly CreateProductFolder $createProductFolder,
        private readonly BuildProductContentsFromPayload $buildProductContentsFromPayload,
        private readonly CreateCategoryFolder $createCategoryFolder,
        private readonly CreateCategoryFolderLevelLabel $createCategoryFolderLevelLabel,
    ) {
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    public function __invoke(
        UpdateRequest $updateRequest,
        AssetResource $parentAssetResource,
    ): void {
        $rootProductFolder = $updateRequest->parentAssetResourceFolderPath;

        $organization = Organization::getById($updateRequest->organizationId);

        if (!($organization instanceof Organization)) {
            throw new \Exception(message: 'Organization does not exists.');
        }

        $parentProductFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Products->readable())
            ->addConditionParam('o_path = ?', $rootProductFolder)
            ->current();

        if (!($parentProductFolder instanceof DataObject)) {
            $parentProductFolder = ($this->createProductFolder)($organization, $rootProductFolder);
        }

        $productData = (array) json_decode($updateRequest->productData, true);

        $categories = $productData['productCategories'] ?? null;

        $productFromPayload = new ProductFromPayload(
            productName: $productData['productName'] ?? '',
            productEAN: $productData['productEAN'] ?? '',
            productSKU: $productData['productSKU'] ?? '',
            productAttributes: $productData['productAttributes'] ?? [],
            productNetContentStatement: $productData['productNetContentStatement'] ?? '',
            productNetContents: $productData['productNetContents'] ?? [],
            productNetUnitContents: $productData['productNetUnitContents'] ?? [],
            productCategories: new CategoryFromPayload(
                brand: $categories['brand'] ?? '',
                campaign: $categories['campaign'] ?? '',
                market: $categories['market'] ?? '',
                segment: $categories['segment'] ?? '',
                platform: $categories['platform'] ?? '',
            )
        );

        $product = null;

        if (!empty($productFromPayload->productEAN)) {
            $product = (new Product\Listing())
                ->addConditionParam('EAN = ?', $productFromPayload->productEAN)
                ->addConditionParam('o_path = ?', $rootProductFolder . AssetResourceOrganizationFolderNames::Products->readable() . '/')
                ->current();
        }

        if ($product instanceof Product) {
            $this->updateProduct($product, $productFromPayload, $organization, $parentAssetResource, $parentProductFolder);

            return;
        }

        if (!empty($productFromPayload->productSKU)) {
            $product = (new Product\Listing())
                ->addConditionParam('SKU = ?', $productFromPayload->productSKU)
                ->addConditionParam('o_path = ?', $rootProductFolder . AssetResourceOrganizationFolderNames::Products->readable() . '/')
                ->current();
        }

        if ($product instanceof Product) {
            $this->updateProduct($product, $productFromPayload, $organization, $parentAssetResource, $parentProductFolder);

            return;
        }

        if (empty($productFromPayload->productSKU) && empty($productFromPayload->productEAN)) {
            return;
        }

        $this->createProduct(
            $productFromPayload,
            $organization,
            $parentAssetResource,
            $parentProductFolder
        );
    }

    /**
     * @throws \Exception
     */
    private function updateProduct(Product $product, ProductFromPayload $productFromPayload, Organization $organization, AssetResource $parentAssetResource, DataObject $parentProductFolder): void
    {
        if (empty($product->getName())) {
            $product->setName($productFromPayload->productName);
        }

        if (empty($product->getEAN())) {
            $product->setEAN($productFromPayload->productEAN);
        }

        if (empty($product->getSKU())) {
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

        if (empty($product->getNetContentStatement())) {
            $product->setNetContentStatement($productFromPayload->productNetContentStatement);
        }

        ($this->buildProductContentsFromPayload)($product, $productFromPayload, true);

        if ($productFromPayload->productCategories instanceof CategoryFromPayload) {
            $product->setCategories($this->updateCategories($productFromPayload->productCategories, $organization, $product));
        }

        $productKey = null;

        if (!empty($productFromPayload->productEAN)) {
            $productKey = $productFromPayload->productEAN;
        }

        if (!empty($productFromPayload->productSKU) && empty($productKey)) {
            $productKey = $productFromPayload->productSKU;
        }

        if (empty($productKey)) {
            return;
        }

        $product->setKey($productKey);

        $assetResources = array_values(array_filter(array_unique([...$product->getAssets(), $parentAssetResource])));

        $product->setAssets($assetResources);
        $product->setParentId((int) $parentProductFolder->getId());
        $product->setPublished(true);

        $product->save();
    }

    /**
     * @throws \Exception
     */
    private function createProduct(ProductFromPayload $productFromPayload, Organization $organization, AssetResource $parentAssetResource, DataObject $parentProductFolder): void
    {
        $product = new Product();

        if (empty($product->getName())) {
            $product->setName($productFromPayload->productName);
        }

        if (empty($product->getEAN())) {
            $product->setEAN($productFromPayload->productEAN);
        }

        if (empty($product->getSKU())) {
            $product->setSKU($productFromPayload->productSKU);
        }

        $productKey = null;

        if (!empty($productFromPayload->productEAN)) {
            $productKey = $productFromPayload->productEAN;
        }

        if (!empty($productFromPayload->productSKU) && empty($productKey)) {
            $productKey = $productFromPayload->productSKU;
        }

        if (empty($productKey)) {
            return;
        }

        $product->setKey($productKey);

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

        ($this->buildProductContentsFromPayload)($product, $productFromPayload, false);

        if ($productFromPayload->productCategories instanceof CategoryFromPayload) {
            $product->setCategories($this->updateCategories($productFromPayload->productCategories, $organization, $product));
        }

        $assetResources = array_values(array_filter(array_unique([...$product->getAssets(), $parentAssetResource])));

        $product->setAssets($assetResources);
        $product->setParentId((int) $parentProductFolder->getId());
        $product->setPublished(true);

        $product->save();
    }

    /**
     * @return array<int, Category>
     *
     * @throws \Exception
     */
    public function updateCategories(CategoryFromPayload $categoryFromPayload, Organization $organization, Product $product): array
    {
        $categories = [];

        foreach ($categoryFromPayload->toArray() as $levelLabel => $productCategory) {
            if (empty($productCategory)) {
                continue;
            }

            $levelLabelName = ucfirst($levelLabel).'s';

            $categoryNames = array_column(array: CategoryNames::cases(), column_key: 'name');

            $isValidCategoryName = in_array(needle: $levelLabelName, haystack: $categoryNames);

            if (!$isValidCategoryName) {
                continue;
            }

            $rootCategoryFolder = $organization->getObjectFolder() . '/';

            $categoriesName = AssetResourceOrganizationFolderNames::Categories->readable();

            $parentCategoryFolder = (new DataObject\Listing())
                ->addConditionParam('o_key = ?', $categoriesName)
                ->addConditionParam('o_path = ?', $rootCategoryFolder)
                ->current();

            if (!($parentCategoryFolder instanceof DataObject)) {
                $parentCategoryFolder = ($this->createCategoryFolder)($organization, $rootCategoryFolder);
            }

            $categoryFolderLevelLabel = (new DataObject\Listing())
                ->addConditionParam('o_key = ?', $levelLabelName)
                ->addConditionParam('o_path = ?', $rootCategoryFolder . "$categoriesName/")
                ->current();

            if (!($categoryFolderLevelLabel instanceof DataObject)) {
                $categoryFolderLevelLabel = ($this->createCategoryFolderLevelLabel)($organization, $parentCategoryFolder, $levelLabelName);
            }

            $category = (new Category\Listing())
                ->addConditionParam('o_key = ?', $productCategory)
                ->addConditionParam('o_path = ?', $rootCategoryFolder . "$categoriesName/$levelLabelName/")
                ->current();

            if (!($category instanceof Category)) {
                $category = new Category();

                $category->setOrganization($organization);
                $category->setLevelLabel(ucfirst($levelLabel));
                $category->setReportingType(ucfirst($levelLabel));
                $category->setParentId((int) $categoryFolderLevelLabel->getId());
                $category->setKey($productCategory);
                $category->setPublished(true);
            }

            if (empty($category->getOrganization())) {
                $category->setOrganization($organization);
            }

            if (empty($category->getReportingType())) {
                $category->setReportingType(ucfirst($levelLabel));
            }

            if (empty($category->getLevelLabel())) {
                $category->setLevelLabel(ucfirst($levelLabel));
            }

            if (empty($category->getKey())) {
                $category->setKey($productCategory);
            }

            if ($categoryFolderLevelLabel instanceof DataObject) {
                $category->setParentId((int) $categoryFolderLevelLabel->getId());
            }

            $category->setPublished(true);

            $category->save();

            if ($category instanceof Category) {
                $categories[] = $category;
            }
        }

        return array_values(array_unique([...$categories, ...$product->getCategories()]));
    }
}
