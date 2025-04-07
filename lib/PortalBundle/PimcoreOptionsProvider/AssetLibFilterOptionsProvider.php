<?php

declare(strict_types=1);

namespace Froq\PortalBundle\PimcoreOptionsProvider;

use Froq\PortalBundle\Exception\ES\EsFilterOptionsProviderException;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;
use Youwe\PimcoreElasticsearchBundle\Service\IndexListingServiceInterface;

class AssetLibFilterOptionsProvider implements SelectOptionsProviderInterface
{
    public function __construct(private readonly IndexListingServiceInterface $esIndexListingManager, private readonly string $esIndexIdAssetLib)
    {
    }

    /**
     * @param array<int, string> $context
     * @param Data $fieldDefinition
     *
     * @return array<int, array<string, string>>
     */
    public function getOptions($context, $fieldDefinition): array
    {
        $options = $this->getKeyValues();
        $this->validate($options);

        return $options;
    }

    /**
     * Returns the value which is defined in the 'Default value' field
     *
     * @param array<int, string> $context
     * @param Data $fieldDefinition
     *
     * @return Data|mixed
     */
    public function getDefaultValue($context, $fieldDefinition): mixed
    {
        return $fieldDefinition->getDefaultValue(); /** @phpstan-ignore-line */
    }

    /**
     * @param array<int, string> $context
     * @param Data $fieldDefinition
     *
     * @return bool
     */
    public function hasStaticOptions($context, $fieldDefinition): bool
    {
        return true;
    }

    /**
     * @param array<int, array<string, string>> $options
     */
    private function validate(array $options): void
    {
        $assetLibraryMapping = $this->esIndexListingManager->getIndex($this->esIndexIdAssetLib)?->getMapping()->getDefinition();
        foreach ($options as $option) {
            if (!isset($assetLibraryMapping[$option['value']])) {
                throw EsFilterOptionsProviderException::undefinedEsMappingException(
                    self::class,
                    $option['value'],
                    $this->esIndexIdAssetLib
                );
            }
        }
    }

    /**
     * @return array<int, array<string, string>>
     */
    public static function getKeyValues(): array
    {
        $libFilterOptions = [
            ['key' => 'Asset Creation Date', 'value' => 'asset_creation_date'],
            ['key' => 'Asset Resource Creation Date', 'value' => 'creation_date'],
            ['key' => 'File Create Date', 'value' => 'file_create_date'],
            ['key' => 'File Modify Date', 'value' => 'file_modify_date'],
            ['key' => 'Asset Resource Name', 'value' => 'asset_resource_name'],
            ['key' => 'Asset Type Name', 'value' => 'asset_type_name'],
            ['key' => 'Brand', 'value' => 'product_category_brand'],
            ['key' => 'Campaign', 'value' => 'product_category_campaign'],
            ['key' => 'Campaign (Text Search)', 'value' => 'product_category_campaign_text'],
            ['key' => 'Market', 'value' => 'product_category_market'],
            ['key' => 'Market (Text Search)', 'value' => 'product_category_market_text'],
            ['key' => 'Customer (Organization name)', 'value' => 'customer'],
            ['key' => 'Epson Material', 'value' => 'epsonmaterial'],
            ['key' => 'File Name', 'value' => 'file_name'],
            ['key' => 'File Name (Text Search)', 'value' => 'file_name_text'],
            ['key' => 'GMG Flow', 'value' => 'gmgflow'],
            ['key' => 'Grammage (Product Attribute)', 'value' => 'grammage'],
            ['key' => 'Net Content Statement', 'value' => 'net_content_statement'],
            ['key' => 'Net Contents (millilitre)', 'value' => 'net_contents_ml'],
            ['key' => 'Net Contents (gram)', 'value' => 'net_contents_g'],
            ['key' => 'Net Contents (pieces)', 'value' => 'net_contents_pcs'],
            ['key' => 'Net Unit Contents (millilitre)', 'value' => 'net_unit_contents_ml'],
            ['key' => 'Net Unit Contents (gram)', 'value' => 'net_unit_contents_g'],
            ['key' => 'Height', 'value' => 'height'],
            ['key' => 'KeyLineNumber', 'value' => 'keylinenumber'],
            ['key' => 'Packshot Angle', 'value' => 'angle'],
            ['key' => 'Packshot Type', 'value' => 'packshottype'],
            ['key' => 'Packshot Quality', 'value' => 'packshotquality'],
            ['key' => 'PDF Text', 'value' => 'pdf_text'],
            ['key' => 'Printer Code', 'value' => 'printer_code'],
            ['key' => 'Printing Material', 'value' => 'printingmaterial'],
            ['key' => 'Printing Process', 'value' => 'printingprocess'],
            ['key' => 'Printing Workflow', 'value' => 'printing_workflow'],
            ['key' => 'Product EAN', 'value' => 'product_ean'],
            ['key' => 'Product EAN (Text Search)', 'value' => 'product_ean_text'],
            ['key' => 'Product Name', 'value' => 'product_name'],
            ['key' => 'Product Name (Text Search)', 'value' => 'product_name_text'],
            ['key' => 'Product SKU', 'value' => 'product_sku'],
            ['key' => 'Product SKU (Text Search)', 'value' => 'product_sku_text'],
            ['key' => 'Project Contact Role: froq_project_owner', 'value' => 'froq_project_owner'],
            ['key' => 'Project Contact Role: project_owner', 'value' => 'project_owner'],
            ['key' => 'Project Froq Name', 'value' => 'project_froq_name'],
            ['key' => 'Project Froq Name (Text Search)', 'value' => 'project_froq_name_text'],
            ['key' => 'Project froq_project_number', 'value' => 'project_froq_project_number'],
            ['key' => 'Project froq_project_number (Text Search)', 'value' => 'project_froq_project_number_text'],
            ['key' => 'Project Name', 'value' => 'project_name'],
            ['key' => 'Project Name (Text Search)', 'value' => 'project_name_text'],
            ['key' => 'Project pim_project_number', 'value' => 'project_pim_project_number'],
            ['key' => 'Project pim_project_number (Text Search)', 'value' => 'project_pim_project_number_text'],
            ['key' => 'Segment', 'value' => 'product_category_segment'],
            ['key' => 'Shape', 'value' => 'shape'],
            ['key' => 'Software', 'value' => 'software'],
            ['key' => 'Substrate Material', 'value' => 'substrate_material'],
            ['key' => 'Upload Name', 'value' => 'upload_name'],
            ['key' => 'Width', 'value' => 'width'],
            ['key' => 'Materials', 'value' => 'materials'],
            ['key' => 'Packtype', 'value' => 'packtype'],
            ['key' => 'Platform', 'value' => 'product_category_platform'],
            ['key' => 'Platform (Text Search)', 'value' => 'product_category_platform_text'],
            ['key' => 'Shape Code', 'value' => 'shapecode'],
            ['key' => 'Shape Code (Text Search)', 'value' => 'shapecode_text'],
            ['key' => 'Shapes', 'value' => 'shapes'],
            ['key' => 'Tags', 'value' => 'tags'],
            ['key' => 'Packaging', 'value' => 'packaging'],
            ['key' => 'Pack range', 'value' => 'packrange'],
            ['key' => 'Product Type', 'value' => 'producttype'],
            ['key' => 'Volumes', 'value' => 'volumes'],
        ];

        usort($libFilterOptions, function ($firstElement, $secondElement) {
            return strcmp($firstElement['key'], $secondElement['key']);
        });

        return $libFilterOptions;

    }
}
