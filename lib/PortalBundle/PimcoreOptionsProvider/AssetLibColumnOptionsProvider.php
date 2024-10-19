<?php

declare(strict_types=1);

namespace Froq\PortalBundle\PimcoreOptionsProvider;

use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider\SelectOptionsProviderInterface;

class AssetLibColumnOptionsProvider implements SelectOptionsProviderInterface
{
    public const ASSET_LIB_COLUMN_THUMBNAIL = 'thumbnail';
    public const ASSET_LIB_COLUMN_FILE_NAME = 'file_name';
    public const ASSET_LIB_COLUMN_ASSET_NAME = 'asset_resource_name';
    public const ASSET_LIB_COLUMN_SKU = 'product_sku';
    public const ASSET_LIB_COLUMN_TYPE = 'asset_type_name';
    public const ASSET_LIB_COLUMN_PROJECT_NAME = 'project_name';
    public const ASSET_LIB_COLUMN_ASSET_RESOURCE_CREATION_DATE = 'creation_date';
    public const ASSET_LIB_COLUMN_ASSET_CREATION_DATE = 'asset_creation_date';
    public const ASSET_LIB_COLUMN_FILE_CREATE_DATE = 'file_create_date';
    public const ASSET_LIB_COLUMN_PRODUCT_NAME = 'product_name';
    public const ASSET_LIB_COLUMN_PRODUCT_EAN = 'product_ean';
    public const ASSET_LIB_COLUMN_FROQ_PROJECT_NUMBER = 'froq_project_number';
    public const ASSET_LIB_COLUMN_PIM_PROJECT_NUMBER = 'pim_project_number';

    /**
     * @param array<int, string> $context
     * @param Data $fieldDefinition
     *
     * @return array<int, array<string, string>>
     */
    public function getOptions($context, $fieldDefinition): array
    {
        return $this->getKeyValues();
    }

    /**
     * Returns the value which is defined in the 'Default value' field
     *
     * @param array<int, string> $context
     * @param Data $fieldDefinition
     *
     * @return Data|mixed
     */
    public function getDefaultValue($context, $fieldDefinition)
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
     * @return array<int, array<string, string>> $options
     */
    public static function getKeyValues(): array
    {
        $libColumnOptions = [
            ['key' => 'Asset Creation Date', 'value' => self::ASSET_LIB_COLUMN_ASSET_CREATION_DATE],
            ['key' => 'Asset Resource Creation Date', 'value' => self::ASSET_LIB_COLUMN_ASSET_RESOURCE_CREATION_DATE],
            ['key' => 'File Create Date', 'value' => self::ASSET_LIB_COLUMN_FILE_CREATE_DATE],
            ['key' => 'Asset Resource Type', 'value' => self::ASSET_LIB_COLUMN_TYPE],
            ['key' => 'Asset Name', 'value' => self::ASSET_LIB_COLUMN_ASSET_NAME],
            ['key' => 'File Name', 'value' => self::ASSET_LIB_COLUMN_FILE_NAME],
            ['key' => 'Project Name', 'value' => self::ASSET_LIB_COLUMN_PROJECT_NAME],
            ['key' => 'Product SKU', 'value' => self::ASSET_LIB_COLUMN_SKU],
            ['key' => 'Thumbnail', 'value' => self::ASSET_LIB_COLUMN_THUMBNAIL],
            ['key' => 'Product Name', 'value' => self::ASSET_LIB_COLUMN_PRODUCT_NAME],
            ['key' => 'Product EAN', 'value' => self::ASSET_LIB_COLUMN_PRODUCT_EAN],
            ['key' => 'FroQ Project Number', 'value' => self::ASSET_LIB_COLUMN_FROQ_PROJECT_NUMBER],
            ['key' => 'Pim Project Number', 'value' => self::ASSET_LIB_COLUMN_PIM_PROJECT_NUMBER],
        ];
        usort($libColumnOptions, function ($firstElement, $secondElement) {
            return strcmp($firstElement['key'], $secondElement['key']);
        });

        return $libColumnOptions;
    }
}
