<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Security;

final class AssetPreviewHasher
{
    public function __construct(
        private readonly string $thumbnailEncryptionSecret
    ) {
    }

    public function hash(int $assetId): string
    {
        return substr(hash_hmac('sha256', (string) $assetId, $this->thumbnailEncryptionSecret), 0, 16);
    }

    public function verify(int $assetId, string $hash): bool
    {
        return hash_equals($this->hash($assetId), $hash);
    }
}
