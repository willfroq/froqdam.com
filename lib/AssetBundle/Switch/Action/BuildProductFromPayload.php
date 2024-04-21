<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Froq\PortalBundle\Repository\ProductRepository;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductAttributes;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductContents;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\QuantityValue\Unit;

final class BuildProductFromPayload
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull,
        private readonly BuildCategoryFromPayload $buildCategoryFromPayload,
    )
    {
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function __invoke(SwitchUploadRequest $switchUploadRequest, AssetResource $assetResource, Organization $organization): void
    {
        $rootProductFolder = $organization->getObjectFolder() . '/';

        $parentProductFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Products->name)
            ->addConditionParam('o_path = ?', $rootProductFolder)
            ->current();

        if (!($parentProductFolder instanceof DataObject)) {
            return;
        }

        $payload = (array) json_decode($switchUploadRequest->productData, true);

        if (empty($payload) || ($this->allPropsEmptyOrNull)($payload)) {
            return;
        }

        $assetResourceId = (string) $assetResource->getId();

        $product = Product::getById($this->productRepository->getRelatedProductId($assetResourceId));

        if (!($product instanceof Product)) {
            $product = new Product();
        }

        if (isset($payload['productName'])) {
            $product->setName($payload['productName']);
        }
        if (isset($payload['productEAN'])) {
            $product->setEAN($payload['productEAN']);
        }
        if (isset($payload['productSKU'])) {
            $product->setSKU($payload['productSKU']);
        }

        if (isset($payload['productAttributes']) && is_array($payload['productAttributes'])) {
            $fieldCollectionItems = [];

            foreach ($payload['productAttributes'] as $item) {
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

        if (isset($payload['productNetContentStatement'])) {
            $product->setNetContentStatement($payload['productNetContentStatement']);
        }

        if (isset($payload['productNetContents']) && is_array($payload['productNetContents'])) {
            $fieldCollectionItems = [];

            foreach ($payload['productNetContents'] as $item) {
                if (empty($item)) {
                    continue;
                }

                $unitId = (string) array_key_first($item);

                $value = (float) current($item);

                $unit = Unit::getById($unitId);

                if (!($unit instanceof Unit)) {
                    continue;
                }

                if (!is_numeric($value)) {
                    continue;
                }

                $productAttributes = new ProductContents();

                $quantityValue = new QuantityValue();
                $quantityValue->setUnitId($unitId);
                $quantityValue->setValue($value);

                $productAttributes->setNetContent($quantityValue);

                $fieldCollectionItems[] = $productAttributes;
            }

            $netContentsFieldCollection = new Fieldcollection();
            $netContentsFieldCollection->setItems($fieldCollectionItems);

            $product->setNetContents($netContentsFieldCollection);
        }

        if (isset($payload['productNetUnitContents']) && is_array($payload['productNetUnitContents'])) {
            $fieldCollectionItems = [];

            foreach ($payload['productNetUnitContents'] as $item) {
                if (empty($item)) {
                    continue;
                }

                $unitId = (string) array_key_first($item);

                $value = (float) current($item);

                $unit = Unit::getById($unitId);

                if (!($unit instanceof Unit)) {
                    continue;
                }

                if (!is_numeric($value)) {
                    continue;
                }

                $productAttributes = new ProductContents();

                $quantityValue = new QuantityValue();
                $quantityValue->setUnitId($unitId);
                $quantityValue->setValue($value);

                $productAttributes->setNetContent($quantityValue);

                $fieldCollectionItems[] = $productAttributes;
            }

            $netUnitContentsFieldCollection = new Fieldcollection();
            $netUnitContentsFieldCollection->setItems($fieldCollectionItems);

            $product->setNetUnitContents($netUnitContentsFieldCollection);
        }

        if (isset($payload['productCategories'])) {
//            $product->setCategories(($this->buildCategoryFromPayload)($payload, $organization, $product));
        }

        $assetResources = [...$product->getAssets(), $assetResource];

        $product->setAssets($assetResources);
        $product->setParentId((int) $parentProductFolder->getId());
        $product->setKey(pathinfo($switchUploadRequest->filename, PATHINFO_FILENAME));
        $product->setPublished(true);

        $product->save();
    }
}
