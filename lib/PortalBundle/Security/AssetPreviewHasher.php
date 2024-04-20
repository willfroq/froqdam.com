<?php

namespace Froq\PortalBundle\Security;

class AssetPreviewHasher
{
    public function __construct(private readonly string $thumbnailEncryptionSecret)
    {
    }

    public function hash(int $assetId): string
    {
        return hash('md5', $assetId . $this->thumbnailEncryptionSecret);
    }

    public function verify(int $assetId, string $hash): bool
    {
        return hash_equals($this->hash($assetId), $hash);
    }
}
