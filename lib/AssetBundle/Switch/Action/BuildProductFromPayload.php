<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\Action\Processor\CreateProduct;
use Froq\AssetBundle\Switch\Action\Processor\UpdateProduct;
use Froq\AssetBundle\Switch\Action\RelatedObject\CreateProductFolder;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\Handlers\SwitchUploadCriticalErrorHandler;
use Froq\AssetBundle\Switch\ValueObject\CategoryFromPayload;
use Froq\AssetBundle\Switch\ValueObject\ProductFromPayload;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

final class BuildProductFromPayload
{
    public function __construct(
        private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull,
        private readonly CreateProductFolder $createProductFolder,
        private readonly CreateProduct $createProduct,
        private readonly UpdateProduct $updateProduct,
        private readonly SwitchUploadCriticalErrorHandler $switchUploadCriticalErrorHandler,
    ) {
    }

    /**
     * @param array<int, string> $actions
     * @param array<int, AssetResource> $assetResources
     *
     * @throws TransportExceptionInterface
     * @throws \Exception*/
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

        $parentAssetResource = current($assetResources);

        if (!($parentAssetResource instanceof AssetResource)) {
            ($this->switchUploadCriticalErrorHandler)($switchUploadRequest);

            throw new \Exception(message: 'BuildProductFromPayload: No container folder i.e. /Customers/org-name/Assets/filename');
        }

        $product = (new Product\Listing())
            ->addConditionParam('EAN = ?', $productFromPayload->productEAN)
            ->addConditionParam('o_path = ?', '/Customers/' . $organization->getName() . '/' . AssetResourceOrganizationFolderNames::Products->readable() . '/')
            ->current();

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

            return;
        }

        $product = (new Product\Listing())
            ->addConditionParam('SKU = ?', $productFromPayload->productEAN)
            ->addConditionParam('o_path = ?', '/Customers/' . $organization->getName() . '/' . AssetResourceOrganizationFolderNames::Products->readable() . '/')
            ->current();

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

            return;
        }

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
