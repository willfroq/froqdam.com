<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\Action\Processor\CreateProduct;
use Froq\AssetBundle\Switch\Action\Processor\UpdateProduct;
use Froq\AssetBundle\Switch\Action\RelatedObject\CreateProductFolder;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\ValueObject\CategoryFromPayload;
use Froq\AssetBundle\Switch\ValueObject\ProductFromPayload;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;

final class BuildProductFromPayload
{
    public function __construct(
        private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull,
        private readonly CreateProductFolder $createProductFolder,
        private readonly CreateProduct $createProduct,
        private readonly UpdateProduct $updateProduct,
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
        array $actions,
    ): void {
        $rootProductFolder = $organization->getObjectFolder() . '/';

        $parentProductFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Products->readable())
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

        $product = Product::getByEAN((string) $productFromPayload->productEAN)?->current(); /** @phpstan-ignore-line */
        if ($product instanceof Product) {
            ($this->updateProduct)(
                $product,
                $organization,
                $productFromPayload,
                $assetResources,
                $rootProductFolder,
                $switchUploadRequest,
                $parentProductFolder,
                $actions
            );
        }

        if (!($product instanceof Product)) {
            $product = Product::getBySKU((string) $productFromPayload->productSKU)?->current(); /** @phpstan-ignore-line */
            if ($product instanceof Product) {
                ($this->updateProduct)(
                    $product,
                    $organization,
                    $productFromPayload,
                    $assetResources,
                    $rootProductFolder,
                    $switchUploadRequest,
                    $parentProductFolder,
                    $actions
                );
            }
        }

        if (!($product instanceof Product)) {
            ($this->createProduct)(
                $organization,
                $productFromPayload,
                $assetResources,
                $rootProductFolder,
                $switchUploadRequest,
                $parentProductFolder,
                $actions
            );
        }
    }
}
