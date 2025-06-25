<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Mapper\Action;

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;

final class GetCustomSetting
{
    /**
     * @param array<string, mixed> $mapping
     */
    public function __invoke(AssetResource $assetResourceLatestVersion, array $mapping, string $filterName, string $customSettingName): string
    {
        $asset = $assetResourceLatestVersion->getAsset();

        if (!($asset instanceof Asset)) {
            return '';
        }

        $customSettings = $asset->getCustomSettings();

        if (!(isset($customSettings[$customSettingName]))) {
            return '';
        }

        return (string) $customSettings[$customSettingName];
    }
}
