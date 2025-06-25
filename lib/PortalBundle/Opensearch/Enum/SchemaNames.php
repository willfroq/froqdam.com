<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Enum;

enum SchemaNames: int
{
    case GroupAssetLibrarySettings = 1;

    public function readable(): string
    {
        return match ($this) {
            self::GroupAssetLibrarySettings => 'GroupAssetLibrarySettings',
        };
    }
}
