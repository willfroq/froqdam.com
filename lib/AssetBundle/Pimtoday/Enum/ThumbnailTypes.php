<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Enum;

enum ThumbnailTypes: string
{
    case Grid = 'portal_asset_library_item_grid';
    case List = 'portal_asset_library_item';
    case Preview = 'portal_asset_detail_preview';
}
