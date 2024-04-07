<?php

namespace Froq\AssetBundle\Message;

class GenerateAssetThumbnailsMessage
{
    private int $assetId;
    /** @var array<int, string> $thumbnails */
    private array $thumbnails;
    private bool $force;

    /**
     * @param int $assetId
     * @param array<int, string> $thumbnails
     * @param bool $force
     */
    public function __construct(int $assetId, array $thumbnails = [], bool $force = false)
    {
        $this->assetId = $assetId;
        $this->thumbnails = $thumbnails;
        $this->force = $force;
    }

    public function getAssetId(): int
    {
        return $this->assetId;
    }

    /** @return array<int, string> */
    public function getThumbnails(): array
    {
        return $this->thumbnails;
    }

    public function getForce(): bool
    {
        return $this->force;
    }
}
