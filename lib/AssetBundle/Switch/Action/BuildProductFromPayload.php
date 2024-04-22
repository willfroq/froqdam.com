<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Froq\AssetBundle\Utility\IsPathExists;
use Froq\PortalBundle\Repository\ProductRepository;
use Pimcore\Log\ApplicationLogger;
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
        private readonly ApplicationLogger $logger,
    ) {
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     *
     * @param array<int, string> $actions
     */
    public function __invoke(SwitchUploadRequest $switchUploadRequest, AssetResource $assetResource, Organization $organization, array $actions): void
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

        if (isset($payload['productNetContentStatement']) && is_string($payload['productNetContentStatement'])) {
            $product->setNetContentStatement($payload['productNetContentStatement']);
        }

        ($this->buildProductContentsFromPayload)($product, $payload);

        if (isset($payload['productCategories'])) {
            $product->setCategories(($this->buildCategoryFromPayload)($payload, $organization, $product, $switchUploadRequest, $actions));
        }

        $assetResources = array_unique([...$product->getAssets(), $assetResource]);

        $productPath = $rootProductFolder.AssetResourceOrganizationFolderNames::Products->name;

        if (($this->isPathExists)($switchUploadRequest, $productPath)) {
            $message = sprintf('Related product NOT created. %s path already exists, this has to be unique.', $productPath);

            $actions[] = $message;

            $this->logger->error(message: $message . implode(separator: ',', array: $actions), context: [
                'component' => $switchUploadRequest->eventName
            ]);
        }

        if (!($this->isPathExists)($switchUploadRequest, $productPath)) {
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
