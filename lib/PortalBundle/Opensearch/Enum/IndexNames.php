<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Enum;

enum IndexNames: int
{
    case ColourGuidelineItem = 1;
    case AssetResourceItem = 2;

    public function readable(): string
    {
        return match ($this) {
            self::ColourGuidelineItem => 'colour-guideline-item',
            self::AssetResourceItem => 'asset-resource-item',
        };
    }
}
