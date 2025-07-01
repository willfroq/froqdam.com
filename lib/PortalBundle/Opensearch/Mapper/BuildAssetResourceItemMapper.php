<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Mapper;

use Froq\AssetBundle\Switch\Enum\CategoryNames;
use Froq\PortalBundle\Opensearch\Action\GetYamlConfigFileProperties;
use Froq\PortalBundle\Opensearch\Enum\IndexNames;
use Froq\PortalBundle\Opensearch\Exception\MappingDoesNotMatchException;
use Froq\PortalBundle\Opensearch\Mapper\Action\GetAssetResourceMetadataValues;
use Froq\PortalBundle\Opensearch\Mapper\Action\GetCustomSetting;
use Froq\PortalBundle\Opensearch\Mapper\Action\GetPdfTextLines;
use Froq\PortalBundle\Opensearch\Mapper\Action\GetProductAttributeValues;
use Froq\PortalBundle\Opensearch\Mapper\Action\GetProductCategoryKeys;
use Froq\PortalBundle\Opensearch\Mapper\Action\GetProductFieldValues;
use Froq\PortalBundle\Opensearch\Mapper\Action\GetProductNetContentStatementValues;
use Froq\PortalBundle\Opensearch\Mapper\Action\GetProductNetContentValues;
use Froq\PortalBundle\Opensearch\Mapper\Action\GetProductNetUnitContentValues;
use Froq\PortalBundle\Opensearch\Mapper\Action\GetProjectFieldValues;
use Froq\PortalBundle\Opensearch\Mapper\Action\GetTagCodeValues;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Project;
use Psr\Cache\InvalidArgumentException;

final class BuildAssetResourceItemMapper
{
    public function __construct(
        private readonly GetProductCategoryKeys $getProductCategoryKeys,
        private readonly GetYamlConfigFileProperties $getYamlConfigFileProperties,
        private readonly GetAssetResourceMetadataValues $getAssetResourceMetadataValues,
        private readonly GetProductAttributeValues $getProductAttributeValues,
        private readonly GetProductNetContentStatementValues $getProductNetContentStatementValues,
        private readonly GetProductNetContentValues $getProductNetContentValues,
        private readonly GetProductNetUnitContentValues $getProductNetUnitContentValues,
        private readonly GetCustomSetting $getCustomSetting,
        private readonly GetPdfTextLines $getPdfTextLines,
        private readonly GetProductFieldValues $getProductFieldValues,
        private readonly GetTagCodeValues $getTagCodeValues,
        private readonly GetProjectFieldValues $getProjectFieldValues,
    ) {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws InvalidArgumentException
     * @throws \Exception*/
    public function __invoke(AssetResource $parentAssetResource, AssetResource $assetResourceLatestVersion): array
    {
        $asset = $assetResourceLatestVersion->getAsset();

        $assetResourceName = $assetResourceLatestVersion->getName() ?? '';
        $assetTypeName = $assetResourceLatestVersion->getAssetType()?->getName() ?? '';

        $organizations = $parentAssetResource->getOrganizations();

        $project = current($parentAssetResource->getProjects());

        $mapping = ($this->getYamlConfigFileProperties)(IndexNames::AssetResourceItem->readable());

        $mappedAssetResource = [
            // Denormalized properties for AssetResourceItem
            'assetResourceId' => (int) $assetResourceLatestVersion->getId(),
            'assetId' => (int) $asset?->getId(),
            'parentId' => (int) $parentAssetResource->getId(),
            'filename' => $assetResourceName,
            'assetTypeName' => $assetTypeName,
            'projectName' => $project instanceof Project ? $project->getName() : '',
            'assetResourceCreationDate' => date('l jS \o\f F Y h:i:s A', (int) $assetResourceLatestVersion->getCreationDate()),
            'assetResourceFileCreateDate' => date('l jS \o\f F Y h:i:s A', (int) $assetResourceLatestVersion->getFileCreateDate()?->timestamp),
            'assetResourceFileModifyDate' => date('l jS \o\f F Y h:i:s A', (int) $assetResourceLatestVersion->getFileModifyDate()?->timestamp),
            'assetCreationDate' => date('l jS \o\f F Y h:i:s A', (int) $asset?->getCreationDate()),
            // AssetResource
            'customer' => array_map(fn (Organization|AbstractObject $organization) => $organization instanceof Organization ? (string) $organization->getName() : '', $parentAssetResource->getOrganizations()),
            'creation_date' => (int) $assetResourceLatestVersion->getCreationDate(),
            'file_create_date' => (int) $assetResourceLatestVersion->getFileCreateDate()?->timestamp,
            'file_modify_date' => (int) $assetResourceLatestVersion->getFileModifyDate()?->timestamp,
            'organization_id' => array_map(fn (Organization|AbstractObject $organization) => (int) $organization->getId(), $organizations),
            'asset_resource_name' => $assetResourceName,
            'asset_type_name' => $assetTypeName,
            'file_name' => $assetResourceName,
            'file_name_text' => $assetResourceName,
            'upload_name' => (string) $assetResourceLatestVersion->getUploadName(),
            'pdf_text' => ($this->getPdfTextLines)($assetResourceLatestVersion, $mapping, 'pdf_text'),
            //Asset
            'file_size' => (int) $asset?->getFileSize(),
            'asset_creation_date' => (int) $asset?->getCreationDate(),
            // AssetResourceMetaData
            'epsonmaterial' => ($this->getAssetResourceMetadataValues)($assetResourceLatestVersion, $mapping, 'epsonmaterial'),
            'gmgflow' => ($this->getAssetResourceMetadataValues)($assetResourceLatestVersion, $mapping, 'gmgflow'),
            'shape' => ($this->getAssetResourceMetadataValues)($assetResourceLatestVersion, $mapping, 'shape'),
            'shapes' => ($this->getAssetResourceMetadataValues)($assetResourceLatestVersion, $mapping, 'shapes'),
            'software' => ($this->getAssetResourceMetadataValues)($assetResourceLatestVersion, $mapping, 'software'),
            'substrate_material' => ($this->getAssetResourceMetadataValues)($assetResourceLatestVersion, $mapping, 'substrate_material'),
            'keylinenumber' => ($this->getAssetResourceMetadataValues)($assetResourceLatestVersion, $mapping, 'keylinenumber'),
            'angle' => ($this->getAssetResourceMetadataValues)($assetResourceLatestVersion, $mapping, 'angle'),
            'packshottype' => ($this->getAssetResourceMetadataValues)($assetResourceLatestVersion, $mapping, 'packshottype'),
            'printer_code' => ($this->getAssetResourceMetadataValues)($parentAssetResource, $mapping, 'printer_code'),
            'printingmaterial' => ($this->getAssetResourceMetadataValues)($parentAssetResource, $mapping, 'printingmaterial'),
            'printing_process' => ($this->getAssetResourceMetadataValues)($parentAssetResource, $mapping, 'printing_process'),
            'printingprocess' => ($this->getAssetResourceMetadataValues)($parentAssetResource, $mapping, 'printingprocess'),
            'printing_workflow' => ($this->getAssetResourceMetadataValues)($parentAssetResource, $mapping, 'printing_workflow'),
            'materials' => ($this->getAssetResourceMetadataValues)($assetResourceLatestVersion, $mapping, 'materials'),
            'packtype' => ($this->getAssetResourceMetadataValues)($assetResourceLatestVersion, $mapping, 'packtype'),
            'shapecode' => ($this->getAssetResourceMetadataValues)($assetResourceLatestVersion, $mapping, 'shapecode'),
            'shapecode_text' => ($this->getAssetResourceMetadataValues)($assetResourceLatestVersion, $mapping, 'shapecode_text'),
            'packaging' => ($this->getAssetResourceMetadataValues)($assetResourceLatestVersion, $mapping, 'packaging'),
            'packrange' => ($this->getAssetResourceMetadataValues)($assetResourceLatestVersion, $mapping, 'packrange'),
            'packshotquality' => ($this->getAssetResourceMetadataValues)($assetResourceLatestVersion, $mapping, 'packshotquality'),
            'producttype' => ($this->getAssetResourceMetadataValues)($assetResourceLatestVersion, $mapping, 'producttype'),
            'volumes' => ($this->getAssetResourceMetadataValues)($assetResourceLatestVersion, $mapping, 'volumes'),
            // Product
            'product_category_brand' => ($this->getProductCategoryKeys)($parentAssetResource, CategoryNames::Brand->readable()),
            'product_category_campaign' => ($this->getProductCategoryKeys)($parentAssetResource, CategoryNames::Campaign->readable()),
            'product_category_market' => ($this->getProductCategoryKeys)($parentAssetResource, CategoryNames::Market->readable()),
            'product_category_segment' => ($this->getProductCategoryKeys)($parentAssetResource, CategoryNames::Segment->readable()),
            'product_category_platform' => ($this->getProductCategoryKeys)($parentAssetResource, CategoryNames::Platform->readable()),
            'product_category_brand_text' => ($this->getProductCategoryKeys)($parentAssetResource, CategoryNames::Brand->readable()),
            'product_category_campaign_text' => ($this->getProductCategoryKeys)($parentAssetResource, CategoryNames::Campaign->readable()),
            'product_category_market_text' => ($this->getProductCategoryKeys)($parentAssetResource, CategoryNames::Market->readable()),
            'product_category_segment_text' => ($this->getProductCategoryKeys)($parentAssetResource, CategoryNames::Segment->readable()),
            'product_category_platform_text' => ($this->getProductCategoryKeys)($parentAssetResource, CategoryNames::Platform->readable()),
            'grammage' => ($this->getProductAttributeValues)($parentAssetResource, $mapping, 'grammage'),
            'net_content_statement' => ($this->getProductNetContentStatementValues)($parentAssetResource, $mapping, 'net_content_statement'),
            'net_contents_ml' => ($this->getProductNetContentValues)($parentAssetResource, $mapping, 'net_contents_ml'),
            'net_contents_g' => ($this->getProductNetContentValues)($parentAssetResource, $mapping, 'net_contents_g'),
            'net_contents_pcs' => ($this->getProductNetContentValues)($parentAssetResource, $mapping, 'net_contents_pcs'),
            'net_unit_contents_ml' => ($this->getProductNetUnitContentValues)($parentAssetResource, $mapping, 'net_unit_contents_ml'),
            'net_unit_contents_g' => ($this->getProductNetUnitContentValues)($parentAssetResource, $mapping, 'net_unit_contents_g'),
            'product_ean' => ($this->getProductFieldValues)($parentAssetResource, $mapping, 'product_ean'),
            'product_name' => ($this->getProductFieldValues)($parentAssetResource, $mapping, 'product_name'),
            'product_sku' => ($this->getProductFieldValues)($parentAssetResource, $mapping, 'product_sku'),
            'product_ean_text' => ($this->getProductFieldValues)($parentAssetResource, $mapping, 'product_ean_text'),
            'product_name_text' => ($this->getProductFieldValues)($parentAssetResource, $mapping, 'product_name_text'),
            'product_sku_text' => ($this->getProductFieldValues)($parentAssetResource, $mapping, 'product_sku_text'),
            // Project
            'froq_project_owner' => ($this->getProjectFieldValues)($parentAssetResource, $mapping, 'froq_project_owner'),
            'froq_project_owner_text' => ($this->getProjectFieldValues)($parentAssetResource, $mapping, 'froq_project_owner_text'),
            'project_owner_text' => ($this->getProjectFieldValues)($parentAssetResource, $mapping, 'project_owner_text'),
            'project_owner' => ($this->getProjectFieldValues)($parentAssetResource, $mapping, 'project_owner'),
            'project_froq_name' => ($this->getProjectFieldValues)($parentAssetResource, $mapping, 'project_froq_name'),
            'project_froq_name_text' => ($this->getProjectFieldValues)($parentAssetResource, $mapping, 'project_froq_name_text'),
            'project_froq_project_number' => ($this->getProjectFieldValues)($parentAssetResource, $mapping, 'project_froq_project_number'),
            'project_froq_project_number_text' => ($this->getProjectFieldValues)($parentAssetResource, $mapping, 'project_froq_project_number_text'),
            'project_name' => ($this->getProjectFieldValues)($parentAssetResource, $mapping, 'project_name'),
            'project_name_text' => ($this->getProjectFieldValues)($parentAssetResource, $mapping, 'project_name_text'),
            'project_pim_project_number' => ($this->getProjectFieldValues)($parentAssetResource, $mapping, 'project_pim_project_number'),
            'project_pim_project_number_text' => ($this->getProjectFieldValues)($parentAssetResource, $mapping, 'project_pim_project_number_text'),
            // Tag
            'tags' => ($this->getTagCodeValues)($assetResourceLatestVersion, $mapping, 'tags'),
            // Custom Settings
            'height' => ($this->getCustomSetting)($assetResourceLatestVersion, $mapping, 'height', 'imageHeight'),
            'width' => ($this->getCustomSetting)($assetResourceLatestVersion, $mapping, 'width', 'imageWidth'),
        ];

        if (!empty(array_diff(array_keys($mappedAssetResource), array_keys($mapping)))) {
            throw MappingDoesNotMatchException::mismatch($mappedAssetResource, $mapping);
        }

        return $mappedAssetResource;
    }
}
