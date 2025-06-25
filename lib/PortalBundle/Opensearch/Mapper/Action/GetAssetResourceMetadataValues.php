<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Mapper\Action;

use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Fieldcollection\Data\AssetResourceMetadata;

final class GetAssetResourceMetadataValues
{
    /**
     * @param array<string, mixed> $mapping
     *
     * @return array<int, string>
     */
    public function __invoke(AssetResource $assetResourceLatestVersion, array $mapping, string $fieldCollectionKey): array
    {
        if (!in_array(needle: $fieldCollectionKey, haystack: array_keys($mapping))) {
            return [];
        }

        $values = [];

        foreach ($assetResourceLatestVersion->getMetadata()?->getItems() ?? [] as $metadata) {
            if (!($metadata instanceof AssetResourceMetadata)) {
                continue;
            }

            if ($metadata->getMetadataKey() === $fieldCollectionKey) {
                $values[] = $metadata->getMetadataValue();
            }
        }

        return array_values(array_unique(array_filter($values, fn (?string $value) => $value !== null)));
    }
}
