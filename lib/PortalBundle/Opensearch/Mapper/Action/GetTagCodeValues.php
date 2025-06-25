<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Mapper\Action;

use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Tag;

final class GetTagCodeValues
{
    /**
     * @param array<string, mixed> $mapping
     *
     * @return array<int, string>
     */
    public function __invoke(AssetResource $assetResourceLatestVersion, array $mapping, string $filterName): array
    {
        if (!in_array(needle: $filterName, haystack: array_keys($mapping))) {
            return [];
        }

        $values = [];

        foreach ($assetResourceLatestVersion->getTags() as $tag) {
            if (!($tag instanceof Tag)) {
                continue;
            }

            $values[] = $tag->getCode();
        }

        return array_values(array_unique(array_filter($values, fn (?string $value) => $value !== null)));
    }
}
