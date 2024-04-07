<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\ValueObject\AssetResourceDetail;

use Webmozart\Assert\Assert;

final class CategoryHierarchies
{
    public function __construct(
        public readonly string $productCategorySegmentTableRowLabel,
        public readonly string $productCategorySegmentName,
        public readonly string $productCategorySegmentNameLink,
        public readonly string $productCategoryBrandTableRowLabel,
        public readonly string $productCategoryBrandName,
        public readonly string $productCategoryBrandNameLink,
        public readonly string $productCategoryCampaignTableRowLabel,
        public readonly string $productCategoryCampaignName,
        public readonly string $productCategoryCampaignNameLink,
        public readonly string $productCategoryMarketTableRowLabel,
        public readonly string $productCategoryMarketName,
        public readonly string $productCategoryMarketNameLink,
        public readonly string $productCategoryPlatformTableRowLabel,
        public readonly string $productCategoryPlatformName,
        public readonly string $productCategoryPlatformNameLink,
    ) {
        Assert::string($this->productCategorySegmentTableRowLabel, 'Expected "productCategorySegmentTableRowLabel" to be an string, got %s');
        Assert::string($this->productCategorySegmentName, 'Expected "productCategorySegmentName" to be an string, got %s');
        Assert::string($this->productCategorySegmentNameLink, 'Expected "productCategorySegmentNameLink" to be an string, got %s');
        Assert::string($this->productCategoryBrandTableRowLabel, 'Expected "productCategoryBrandTableRowLabel" to be an string, got %s');
        Assert::string($this->productCategoryBrandName, 'Expected "productCategoryBrandName" to be an string, got %s');
        Assert::string($this->productCategoryBrandNameLink, 'Expected "productCategoryBrandNameLink" to be an string, got %s');
        Assert::string($this->productCategoryCampaignTableRowLabel, 'Expected "productCategoryCampaignTableRowLabel" to be an string, got %s');
        Assert::string($this->productCategoryCampaignName, 'Expected "productCategoryCampaignName" to be an string, got %s');
        Assert::string($this->productCategoryCampaignNameLink, 'Expected "productCategoryCampaignNameLink" to be an string, got %s');
        Assert::string($this->productCategoryMarketTableRowLabel, 'Expected "productCategoryMarketTableRowLabel" to be an string, got %s');
        Assert::string($this->productCategoryMarketName, 'Expected "productCategoryMarketName" to be an string, got %s');
        Assert::string($this->productCategoryMarketNameLink, 'Expected "productCategoryMarketNameLink" to be an string, got %s');
        Assert::string($this->productCategoryPlatformTableRowLabel, 'Expected "productCategoryPlatformTableRowLabel" to be an string, got %s');
        Assert::string($this->productCategoryPlatformName, 'Expected "productCategoryPlatformName" to be an string, got %s');
        Assert::string($this->productCategoryPlatformNameLink, 'Expected "productCategoryPlatformNameLink" to be an string, got %s');
    }
}
