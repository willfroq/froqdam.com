<?php

declare(strict_types=1);

namespace Froq\PortalBundle\DataTransferObject\Request;

use Webmozart\Assert\Assert as AssertProps;

final class SelectedAssetResource
{
    public function __construct(
        public readonly int $id,
        public readonly string $filename,
        public readonly string $assetType,
        public readonly string $projectName,
        public readonly string $thumbnail,
    ) {
        AssertProps::integer($this->id, 'Expected "id" to be an integer, got %s');
        AssertProps::string($this->filename, 'Expected "filename" to be an string, got %s');
        AssertProps::string($this->assetType, 'Expected "assetType" to be an string, got %s');
        AssertProps::string($this->projectName, 'Expected "projectName" to be an string, got %s');
        AssertProps::string($this->thumbnail, 'Expected "thumbnail" to be an string, got %s');
    }
}
