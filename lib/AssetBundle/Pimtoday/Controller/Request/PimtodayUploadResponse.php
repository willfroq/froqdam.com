<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Controller\Request;

use Webmozart\Assert\Assert;

final class PimtodayUploadResponse
{
    public function __construct(
        public readonly string $eventName,
        public readonly string $date,
        public readonly string $assetId,
        public readonly string $assetResourceId,
        public readonly string $uploadedDamProjectId,
    ) {
        Assert::string($this->eventName, 'Expected "filename" to be a string, got %s');
        Assert::string($this->date, 'Expected "date" to be a string, got %s');
        Assert::string($this->assetId, 'Expected "assetId" to be a string, got %s');
        Assert::string($this->assetResourceId, 'Expected "assetResourceId" to be a string, got %s');
        Assert::string($this->uploadedDamProjectId, 'Expected "uploadedDamProjectId" to be a string, got %s');
    }

    /** @return array<string, array<int|string, int|string|null>|string> */
    public function toArray(): array
    {
        return [
            'eventName' => $this->eventName,
            'date' => $this->date,
            'assetId' => $this->assetId,
            'assetResourceId' => $this->assetResourceId,
            'uploadedDamProjectId' => $this->uploadedDamProjectId,
        ];
    }
}
