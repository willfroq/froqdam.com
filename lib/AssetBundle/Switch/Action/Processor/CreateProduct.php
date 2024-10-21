<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\Processor;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Action\BuildCategoryFromPayload;
use Froq\AssetBundle\Switch\Action\BuildProductContentsFromPayload;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\ValueObject\CategoryFromPayload;
use Froq\AssetBundle\Switch\ValueObject\ProductFromPayload;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductAttributes;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;

final class CreateProduct
{
    public function __construct(
        private readonly BuildCategoryFromPayload $buildCategoryFromPayload,
        private readonly BuildProductContentsFromPayload $buildProductContentsFromPayload,
    ) {
    }

    /**
     * @param Organization $organization
     * @param ProductFromPayload $productFromPayload
     * @param string $rootProductFolder
     * @param SwitchUploadRequest $switchUploadRequest
     * @param DataObject $parentProductFolder
     * @param array<int, string> $actions
     *
     * @throws \Exception
     * @throws Exception
     */
    public function __invoke(
        Organization $organization,
        ProductFromPayload $productFromPayload,
        AssetResource $parentAssetResource,
        string $rootProductFolder,
        SwitchUploadRequest $switchUploadRequest,
        DataObject $parentProductFolder,
        array &$actions
    ): void {
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

        if (empty($product->getKey())) {
            $product->setKey($productFromPayload->productEAN . '-' . $productFromPayload->productSKU . '-' . uniqid());
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

        $product->setNetContentStatement($productFromPayload->productNetContentStatement);

        ($this->buildProductContentsFromPayload)($product, $productFromPayload, false);

        if ($productFromPayload->productCategories instanceof CategoryFromPayload) {
            $product->setCategories(($this->buildCategoryFromPayload)($productFromPayload->productCategories, $organization, $product, $switchUploadRequest, $actions));
        }

        $assetResources = array_values(array_filter(array_unique([...$product->getAssets(), $parentAssetResource])));

        $product->setAssets($assetResources);
        $product->setParentId((int) $parentProductFolder->getId());
        $product->setPublished(true);

        $product->save();

        $actions[] = sprintf(
            'Product with ID %d is created with related AssetResource ids: %s',
            $product->getId(),
            implode(',', array_map(fn (AssetResource $assetResource) => $assetResource->getId(), $assetResources))
        );
    }
}
