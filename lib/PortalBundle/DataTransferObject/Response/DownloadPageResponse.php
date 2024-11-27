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
        public readonly string $expiryDate,
    ) {
        AssertProps::isArray($this->assetResources, 'Expected "assetResources" to be an integer, got %s');
        AssertProps::string($this->expiryDate, 'Expected "downloadLink" to be a string, got %s');
    }

    /** @return array<string, AssetResource[]|string> */
    public function toArray(): array
    {
        return [
            'assetResources' => $this->assetResources,
            'expiryDate' => $this->expiryDate,
        ];
    }
}
