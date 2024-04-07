<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\TabSection;

use Webmozart\Assert\Assert;

final class LinkedItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $thumbnail,
        public readonly string $filename,
        public readonly string $productSku,
        public readonly string $assetTypeName,
        public readonly string $projectName,
        public readonly string $linkToItem,
        public readonly string $downloadLink,
    ) {
        Assert::numeric($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::string($this->thumbnail, 'Expected "thumbnail" to be an string, got %s');
        Assert::string($this->filename, 'Expected "filename" to be an string, got %s');
        Assert::string($this->productSku, 'Expected "productSku" to be an string, got %s');
        Assert::string($this->assetTypeName, 'Expected "assetTypeName" to be an string, got %s');
        Assert::string($this->projectName, 'Expected "projectName" to be an string, got %s');
        Assert::string($this->linkToItem, 'Expected "linkToItem" to be an string, got %s');
        Assert::string($this->downloadLink, 'Expected "downloadLink" to be an string, got %s');
    }
}
