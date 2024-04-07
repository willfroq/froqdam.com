<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action\AssetResourceDetail;

use Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection\SettingsSection\BuildProductInfoSectionCollection;
use Froq\PortalBundle\Api\Action\GetBaseUrl;
use Froq\PortalBundle\Api\Enum\ProductInfoSectionItems;
use Froq\PortalBundle\Api\Enum\StructuredTableNames;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\CategoryHierarchies;
use Froq\PortalBundle\Manager\UserSettings\AssetDetail\AssetDetailSettingsManager;
use Froq\PortalBundle\Twig\AssetLibraryExtension;
use Froq\PortalBundle\Twig\PortalDetailExtension;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class BuildCategoryHierarchies
{
    public function __construct(
        private readonly AssetDetailSettingsManager $assetDetailSettingsManager,
        private readonly PortalDetailExtension $portalDetailExtension,
        private readonly BuildProductInfoSectionCollection $buildProductInfoSectionCollection,
        private readonly AssetLibraryExtension $assetLibraryExtension,
        private readonly GetBaseUrl $getBaseUrl,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function __invoke(Product $product, GroupAssetLibrarySettings $userSettings, User $user): CategoryHierarchies
    {
        $categoryHierarchies = $this->portalDetailExtension->portalProductCategoryHierarchies($product);

        $productCategorySegmentTableRowLabel = '';
        $productCategorySegmentName = '';
        $productCategorySegmentNameLink = '';

        $productCategoryBrandTableRowLabel = '';
        $productCategoryBrandName = '';
        $productCategoryBrandNameLink = '';

        $productCategoryCampaignTableRowLabel = '';
        $productCategoryCampaignName = '';
        $productCategoryCampaignNameLink = '';

        $productCategoryMarketTableRowLabel = '';
        $productCategoryMarketName = '';
        $productCategoryMarketNameLink = '';

        $productCategoryPlatformTableRowLabel = '';
        $productCategoryPlatformName = '';
        $productCategoryPlatformNameLink = '';

        if (!empty($categoryHierarchies)) {
            $productInfoSectionCollection = ($this->buildProductInfoSectionCollection)($userSettings);

            foreach ($categoryHierarchies as $label => $categoryHierarchy) {
                if ($productInfoSectionCollection->getItemByName(ProductInfoSectionItems::ProductCategorySegment->readable())?->isEnabled &&
                    strtolower((string) $label) === 'segment'
                ) {
                    $productCategorySegmentTableRowLabel = (string) $this->assetDetailSettingsManager->getAvailableStructuredTableRowLabel(
                        $userSettings,
                        StructuredTableNames::SkuSectionItems->readable(),
                        ProductInfoSectionItems::ProductCategorySegment->readable()
                    );

                    foreach ($categoryHierarchy as $deepestCategoryName => $categories) {
                        $isProductCategorySegmentAvailableForUser = $this->assetLibraryExtension->isFilterAvailableForUser(
                            $user, ProductInfoSectionItems::ProductCategorySegment->readable()
                        );

                        $productCategorySegmentName = (string) $deepestCategoryName;

                        $productCategorySegmentNameLink = $isProductCategorySegmentAvailableForUser ? ($this->getBaseUrl)() . $this->urlGenerator->generate('froq_portal_api.assets', [
                            'code' => $user->getCode(), 'filters' => [
                                    ProductInfoSectionItems::ProductCategorySegment->readable() => [strtolower($productCategorySegmentName)]
                                ]
                            ]) : '';
                    }
                }

                if ($productInfoSectionCollection->getItemByName(ProductInfoSectionItems::ProductCategoryBrand->readable())?->isEnabled &&
                    strtolower((string) $label) === 'brand'
                ) {
                    $productCategoryBrandTableRowLabel = (string) $this->assetDetailSettingsManager->getAvailableStructuredTableRowLabel(
                        $userSettings,
                        StructuredTableNames::SkuSectionItems->readable(),
                        ProductInfoSectionItems::ProductCategoryBrand->readable()
                    );

                    foreach ($categoryHierarchy as $deepestCategoryName => $categories) {
                        $isProductCategoryBrandAvailableForUser = $this->assetLibraryExtension->isFilterAvailableForUser(
                            $user, ProductInfoSectionItems::ProductCategoryBrand->readable()
                        );

                        $productCategoryBrandName = (string) $deepestCategoryName;

                        $productCategoryBrandNameLink = $isProductCategoryBrandAvailableForUser ? ($this->getBaseUrl)() . $this->urlGenerator->generate('froq_portal_api.assets', [
                                'code' => $user->getCode(), 'filters' => [
                                    ProductInfoSectionItems::ProductCategoryBrand->readable() => [strtolower($productCategoryBrandName)]
                                ]
                            ]) : '';
                    }
                }

                if ($productInfoSectionCollection->getItemByName(ProductInfoSectionItems::ProductCategoryCampaign->readable())?->isEnabled &&
                    strtolower((string) $label) === 'campaign'
                ) {
                    $productCategoryCampaignTableRowLabel = (string) $this->assetDetailSettingsManager->getAvailableStructuredTableRowLabel(
                        $userSettings,
                        StructuredTableNames::SkuSectionItems->readable(),
                        ProductInfoSectionItems::ProductCategoryCampaign->readable()
                    );

                    foreach ($categoryHierarchy as $deepestCategoryName => $categories) {
                        $isProductCategoryCampaignAvailableForUser = $this->assetLibraryExtension->isFilterAvailableForUser(
                            $user, ProductInfoSectionItems::ProductCategoryCampaign->readable()
                        );

                        $productCategoryCampaignName = (string) $deepestCategoryName;

                        $productCategoryCampaignNameLink = $isProductCategoryCampaignAvailableForUser ? ($this->getBaseUrl)() . $this->urlGenerator->generate('froq_portal_api.assets', [
                                'code' => $user->getCode(), 'filters' => [
                                    ProductInfoSectionItems::ProductCategoryCampaign->readable() => [strtolower($productCategoryCampaignName)]
                                ]
                            ]) : '';
                    }

                    if ($productInfoSectionCollection->getItemByName(ProductInfoSectionItems::ProductCategoryMarket->readable())?->isEnabled &&
                        strtolower((string) $label) === 'market'
                    ) {
                        $productCategoryMarketTableRowLabel = (string) $this->assetDetailSettingsManager->getAvailableStructuredTableRowLabel(
                            $userSettings,
                            StructuredTableNames::SkuSectionItems->readable(),
                            ProductInfoSectionItems::ProductCategoryMarket->readable()
                        );

                        foreach ($categoryHierarchy as $deepestCategoryName => $categories) {
                            $isProductCategoryMarketAvailableForUser = $this->assetLibraryExtension->isFilterAvailableForUser(
                                $user, ProductInfoSectionItems::ProductCategoryMarket->readable()
                            );

                            $productCategoryMarketName = (string) $deepestCategoryName;

                            $productCategoryCampaignNameLink = $isProductCategoryMarketAvailableForUser ? ($this->getBaseUrl)() . $this->urlGenerator->generate('froq_portal_api.assets', [
                                    'code' => $user->getCode(), 'filters' => [
                                        ProductInfoSectionItems::ProductCategoryMarket->readable() => [strtolower($productCategoryMarketName)]
                                    ]
                                ]) : '';
                        }
                    }

                    if ($productInfoSectionCollection->getItemByName(ProductInfoSectionItems::ProductCategoryPlatform->readable())?->isEnabled &&
                        strtolower((string) $label) === 'platform'
                    ) {
                        $productCategoryPlatformTableRowLabel = (string)$this->assetDetailSettingsManager->getAvailableStructuredTableRowLabel(
                            $userSettings,
                            StructuredTableNames::SkuSectionItems->readable(),
                            ProductInfoSectionItems::ProductCategoryPlatform->readable()
                        );

                        foreach ($categoryHierarchy as $deepestCategoryName => $categories) {
                            $isProductCategoryPlatformAvailableForUser = $this->assetLibraryExtension->isFilterAvailableForUser(
                                $user, ProductInfoSectionItems::ProductCategoryPlatform->readable()
                            );

                            $productCategoryPlatformName = (string)$deepestCategoryName;

                            $productCategoryCampaignNameLink = $isProductCategoryPlatformAvailableForUser ? ($this->getBaseUrl)() . $this->urlGenerator->generate('froq_portal_api.assets', [
                                    'code' => $user->getCode(), 'filters' => [
                                        ProductInfoSectionItems::ProductCategoryPlatform->readable() => [strtolower($productCategoryPlatformName)]
                                    ]
                                ]) : '';
                        }
                    }
                }
            }
        }

        return new CategoryHierarchies(
            productCategorySegmentTableRowLabel: $productCategorySegmentTableRowLabel,
            productCategorySegmentName: $productCategorySegmentName,
            productCategorySegmentNameLink: $productCategorySegmentNameLink,
            productCategoryBrandTableRowLabel: $productCategoryBrandTableRowLabel,
            productCategoryBrandName: $productCategoryBrandName,
            productCategoryBrandNameLink: $productCategoryBrandNameLink,
            productCategoryCampaignTableRowLabel: $productCategoryCampaignTableRowLabel,
            productCategoryCampaignName: $productCategoryCampaignName,
            productCategoryCampaignNameLink: $productCategoryCampaignNameLink,
            productCategoryMarketTableRowLabel: $productCategoryMarketTableRowLabel,
            productCategoryMarketName: $productCategoryMarketName,
            productCategoryMarketNameLink: $productCategoryMarketNameLink,
            productCategoryPlatformTableRowLabel: $productCategoryPlatformTableRowLabel,
            productCategoryPlatformName: $productCategoryPlatformName,
            productCategoryPlatformNameLink: $productCategoryPlatformNameLink,
        );
    }
}
