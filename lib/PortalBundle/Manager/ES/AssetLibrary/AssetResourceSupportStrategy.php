<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\ES\AssetLibrary;

use Froq\PortalBundle\Helper\AssetResourceHierarchyHelper;
use Pimcore\Model\DataObject\AssetResource;
use Youwe\PimcoreElasticsearchBundle\Elasticsearch\Index\SupportStrategyInterface;

class AssetResourceSupportStrategy implements SupportStrategyInterface
{
    public function getSupportedTypes(): array
    {
        return [AssetResource::class];
    }

    public function isIndexed(object $element): bool
    {
        /** @var AssetResource $element */
        return AssetResourceHierarchyHelper::isParent($element);
    }
}
