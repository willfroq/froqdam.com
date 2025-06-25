<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Exception;

final class AssetAndAssetResourceNotInSyncException extends \RuntimeException
{
    public static function notSync(string $assetResourceId): AssetAndAssetResourceNotInSyncException
    {
        return new self(message: sprintf('AssetResource %s without linked Asset is not allowed!', $assetResourceId));
    }
}
