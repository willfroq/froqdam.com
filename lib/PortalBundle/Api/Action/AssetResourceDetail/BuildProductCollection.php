<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action\AssetResourceDetail;

use Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection\SettingsSection\BuildProductInfoSectionCollection;
use Froq\PortalBundle\Api\Action\GetBaseUrl;
use Froq\PortalBundle\Api\Enum\ProductInfoSectionItems;
use Froq\PortalBundle\Api\Enum\SectionFieldTitles;
use Froq\PortalBundle\Api\Enum\StructuredTableNames;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\ProductCollection;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\ProductItem;
use Froq\PortalBundle\Manager\UserSettings\AssetDetail\AssetDetailSettingsManager;
use Froq\PortalBundle\Twig\AssetLibraryExtension;
use Froq\PortalBundle\Twig\PortalDetailExtension;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class BuildProductCollection
{
    public function __construct(
        private readonly PortalDetailExtension $portalDetailExtension,
        private readonly AssetDetailSettingsManager $assetDetailSettingsManager,
        private readonly BuildProductInfoSectionCollection $buildProductInfoSectionCollection,
        private readonly AssetLibraryExtension $assetLibraryExtension,
        private readonly GetBaseUrl $getBaseUrl,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly BuildCategoryHierarchies $buildCategoryHierarchies,
    ) {
    }

    public function __invoke(AssetResource $assetResource, GroupAssetLibrarySettings $userSettings, User $user): ProductCollection
    {
        $products = [];

        $portalAssetResourceProducts = $this->portalDetailExtension->portalAssetResourceProducts($assetResource);

        if (!empty($portalAssetResourceProducts)) {
            $productInfoSectionCollection = ($this->buildProductInfoSectionCollection)($userSettings);

            foreach ($portalAssetResourceProducts as $portalAssetResourceProduct) {
                if (!($portalAssetResourceProduct instanceof Product)) {
                    continue;
                }

                $productNameTableRowLabel = '';
                $name = '';
                $nameLink = '';
                $skuTableRowLabel = '';
                $sku = '';
                $skuLink = '';
                $eanTableRowLabel = '';
                $ean = '';
                $eanLink = '';

                if ($productInfoSectionCollection->getItemByName(ProductInfoSectionItems::ProductName->readable())?->isEnabled) {
                    $productNameTableRowLabel = (string) $this->assetDetailSettingsManager->getAvailableStructuredTableRowLabel(
                        $userSettings,
                        StructuredTableNames::SkuSectionItems->readable(),
                        ProductInfoSectionItems::ProductName->readable()
                    );

                    $isNameAvailableForUser = $portalAssetResourceProduct->getName() &&
                        $this->assetLibraryExtension->isFilterAvailableForUser(
                            $user, ProductInfoSectionItems::ProductName->readable()
                        );

                    $name = $isNameAvailableForUser ? $portalAssetResourceProduct->getName() : '-';

                    $nameLink = $isNameAvailableForUser ? ($this->getBaseUrl)() . $this->urlGenerator->generate('froq_portal_api.assets', [
                        'code' => $user->getCode(), 'filters' => [
                                ProductInfoSectionItems::ProductName->readable() => [strtolower((string) $name)]
                            ]
                        ]) : '';
                }

                if ($productInfoSectionCollection->getItemByName(ProductInfoSectionItems::ProductSku->readable())?->isEnabled) {
                    $skuTableRowLabel = (string) $this->assetDetailSettingsManager->getAvailableStructuredTableRowLabel(
                        $userSettings,
                        StructuredTableNames::SkuSectionItems->readable(),
                        ProductInfoSectionItems::ProductSku->readable()
                    );

                    $isSkuAvailableForUser = $portalAssetResourceProduct->getSKU() &&
                        $this->assetLibraryExtension->isFilterAvailableForUser(
                            $user, ProductInfoSectionItems::ProductSku->readable()
                        );

                    $sku = $isSkuAvailableForUser ? $portalAssetResourceProduct->getSKU() : '-';

                    $skuLink = $isSkuAvailableForUser ? ($this->getBaseUrl)() . $this->urlGenerator->generate('froq_portal_api.assets', [
                        'code' => $user->getCode(), 'filters' => [
                                ProductInfoSectionItems::ProductSku->readable() => [strtolower((string) $sku)]
                            ]
                        ]) : '';
                }

                if ($productInfoSectionCollection->getItemByName(ProductInfoSectionItems::ProductEan->readable())?->isEnabled) {
                    $eanTableRowLabel = (string) $this->assetDetailSettingsManager->getAvailableStructuredTableRowLabel(
                        $userSettings,
                        StructuredTableNames::SkuSectionItems->readable(),
                        ProductInfoSectionItems::ProductEan->readable()
                    );

                    $isEanAvailableForUser = $portalAssetResourceProduct->getEAN() &&
                        $this->assetLibraryExtension->isFilterAvailableForUser(
                            $user, ProductInfoSectionItems::ProductEan->readable()
                        );

                    $ean = $isEanAvailableForUser ? $portalAssetResourceProduct->getEAN() : '-';

                    $eanLink = $isEanAvailableForUser ? ($this->getBaseUrl)() . $this->urlGenerator->generate('froq_portal_api.assets', [
                        'code' => $user->getCode(), 'filters' => [
                                ProductInfoSectionItems::ProductEan->readable() => [strtolower((string) $ean)]
                            ]
                        ]) : '';
                }

                $products[] = new ProductItem(
                    id: (int) $portalAssetResourceProduct->getId(),
                    productNameTableRowLabel: $productNameTableRowLabel,
                    name: (string) $name,
                    nameLink: $nameLink,
                    skuTableRowLabel: $skuTableRowLabel,
                    sku: (string) $sku,
                    skuLink: $skuLink,
                    eanTableRowLabel: $eanTableRowLabel,
                    ean: (string) $ean,
                    eanLink: $eanLink,
                    categoryHierarchies: ($this->buildCategoryHierarchies)($portalAssetResourceProduct, $userSettings, $user)
                );
            }
        }

        return new ProductCollection(
            totalCount: count($products),
            assetDetailConfigLabel: (string) $this->assetDetailSettingsManager->getAvailableSectionLabel(
                $userSettings, SectionFieldTitles::SkuSectionTitle->readable()
            ),
            items: $products
        );
    }
}
