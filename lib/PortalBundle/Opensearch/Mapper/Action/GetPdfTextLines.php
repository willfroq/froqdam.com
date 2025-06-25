<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Mapper\Action;

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;

final class GetPdfTextLines
{
    /**
     * @param array<string, mixed> $mapping
     *
     * @throws \Exception
     */
    public function __invoke(AssetResource $assetResourceLatestVersion, array $mapping, string $filterName): string
    {
        if (!in_array(needle: $filterName, haystack: array_keys($mapping))) {
            return '';
        }

        $asset = $assetResourceLatestVersion->getAsset();

        if (!($asset instanceof Asset\Document)) {
            return '';
        }

        if ($asset->getMimeType() !== 'application/pdf') {
            return '';
        }

        return (string) $assetResourceLatestVersion->getPdfText();
    }
}
