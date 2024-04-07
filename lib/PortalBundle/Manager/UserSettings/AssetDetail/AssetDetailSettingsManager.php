<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\UserSettings\AssetDetail;

use Pimcore\Model\DataObject\GroupAssetLibrarySettings;

class AssetDetailSettingsManager
{
    public static function getAvailableStructuredTableRowLabel(GroupAssetLibrarySettings $settings, string $structuredTableName, string $rowKey): string|null
    {
        $tableGetter = sprintf('get%s', $structuredTableName);
        $table = $settings->$tableGetter();

        $rowLabelGetter = sprintf('get%s__label', $rowKey);
        $label = $table->$rowLabelGetter();

        if ($label && $label !== '') {
            return $label;
        }

        return self::getDefaultStructuredTableRowLabel($settings, $structuredTableName, $rowKey);
    }

    public static function getDefaultStructuredTableRowLabel(GroupAssetLibrarySettings $settings, string $structuredTableName, string $rowKey): string|null
    {
        $tableDefinition = $settings->getClass()?->getFieldDefinition($structuredTableName);
        foreach ($tableDefinition?->getRows() ?? [] as $rowData) { /** @phpstan-ignore-line */
            if ($rowData['key'] === $rowKey) {
                return $rowData['label'];
            }
        }

        return null;
    }

    public static function getAvailableSectionLabel(GroupAssetLibrarySettings $settings, string $sectionFieldName): float|int|null|string
    {
        $sectionFieldGetter = sprintf('get%s', $sectionFieldName);
        $label = $settings->$sectionFieldGetter();

        return ($label && $label !== '') ? $label : self::getDefaultSectionLabel($settings, $sectionFieldName);
    }

    public static function getDefaultSectionLabel(GroupAssetLibrarySettings $settings, string $sectionFieldName): float|int|null|string
    {
        return $settings->getClass()->getFieldDefinition($sectionFieldName)?->getDefaultValue(); /** @phpstan-ignore-line */
    }
}
