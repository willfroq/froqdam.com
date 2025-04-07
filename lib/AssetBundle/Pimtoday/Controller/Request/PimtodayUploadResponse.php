<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Controller\Request;

use Webmozart\Assert\Assert;

final class PimtodayUploadResponse
{
    public function __construct(
        public string $eventName,

        public ?string $date,

        public ?string $filename,

        public ?int $pimtodayProjectId,

        public ?int $damProjectId,

        public ?int $pimtodaySkuId,

        public ?int $damSkuId,

        public ?int $pimtodayDocumentId,

        public ?int $damAssetResourceId,

        public ?int $version,

        public ?int $statusCode,

        /** @var array<int, mixed> $errors */
        public ?array $errors
    ) {
        Assert::string($this->eventName, 'Expected "eventName" to be a string, got %s');
        Assert::string($this->date, 'Expected "date" to be a string, got %s');
        Assert::string($this->filename, 'Expected "filename" to be a string, got %s');
        Assert::nullOrInteger($this->pimtodayProjectId, 'Expected "pimtodayProjectId" to be instance of int, got %s');
        Assert::nullOrInteger($this->damProjectId, 'Expected "damProjectId" to be instance of int, got %s');
        Assert::nullOrInteger($this->pimtodaySkuId, 'Expected "pimtodaySkuId" to be instance of int, got %s');
        Assert::nullOrInteger($this->damSkuId, 'Expected "damSkuId" to be instance of int, got %s');
        Assert::nullOrInteger($this->pimtodayDocumentId, 'Expected "pimtodayDocumentId" to be instance of int, got %s');
        Assert::nullOrInteger($this->damAssetResourceId, 'Expected "damAssetResourceId" to be instance of int, got %s');
        Assert::nullOrInteger($this->version, 'Expected "version" to be instance of int, got %s');
        Assert::nullOrInteger($this->statusCode, 'Expected "statusCode" to be instance of int, got %s');
        Assert::isArray($this->errors, 'Expected "errors" to be instance of array, got %s');
    }

    /** @return array<string, mixed|null> */
    public function toArray(): array
    {
        return [
            'eventName' => $this->eventName,
            'date' => $this->date,
            'filename' => $this->filename,
            'pimtodayProjectId' => $this->pimtodayProjectId,
            'damProjectId' => $this->damProjectId,
            'pimtodaySkuId' => $this->pimtodaySkuId,
            'damSkuId' => $this->damSkuId,
            'pimtodayDocumentId' => $this->pimtodayDocumentId,
            'damAssetResourceId' => $this->damAssetResourceId,
            'version' => $this->version,
            'errors' => $this->errors,
        ];
    }
}
