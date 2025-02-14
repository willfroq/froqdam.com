<?php

declare(strict_types=1);

namespace Froq\PortalBundle\DataTransferObject\Response;

use Pimcore\Model\DataObject\AssetResource;
use Webmozart\Assert\Assert as AssertProps;

final class DownloadPageResponse
{
    public function __construct(
        /** @var array<int, AssetResource> */
        public readonly array $assetResources,
        /** @var array<int, int> */
        public readonly array $assetResourceIds,
        public readonly string $expiryDate,
        public readonly string $uuid,
    ) {
        AssertProps::isArray($this->assetResources, 'Expected "assetResources" to be an array, got %s');
        AssertProps::isArray($this->assetResourceIds, 'Expected "assetResourceIds" to be an array, got %s');
        AssertProps::string($this->expiryDate, 'Expected "expiryDate" to be a string, got %s');
        AssertProps::string($this->uuid, 'Expected "uuid" to be a string, got %s');
    }

    /** @return array<string, AssetResource[]|string|int[]> */
    public function toArray(): array
    {
        return [
            'assetResources' => $this->assetResources,
            'assetResourceIds' => $this->assetResourceIds,
            'expiryDate' => $this->expiryDate,
            'uuid' => $this->uuid,
        ];
    }
}
