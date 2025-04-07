<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\TabSection;

use Webmozart\Assert\Assert;

final class LinkedItem
{
    public function __construct(
        public int $id,
        public string $thumbnail,
        public string $filename,
        public string $productSku,
        public string $assetTypeName,
        public string $projectName,
        public string $downloadLink,
    ) {
        Assert::numeric($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::string($this->thumbnail, 'Expected "thumbnail" to be an string, got %s');
        Assert::string($this->filename, 'Expected "filename" to be an string, got %s');
        Assert::string($this->productSku, 'Expected "productSku" to be an string, got %s');
        Assert::string($this->assetTypeName, 'Expected "assetTypeName" to be an string, got %s');
        Assert::string($this->projectName, 'Expected "projectName" to be an string, got %s');
        Assert::string($this->downloadLink, 'Expected "downloadLink" to be an string, got %s');
    }
}
