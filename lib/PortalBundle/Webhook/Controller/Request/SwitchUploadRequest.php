<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Webhook\Controller\Request;

use Froq\PortalBundle\Api\ValueObject\ValidationError;
use Froq\PortalBundle\Webhook\Validator\AssetTypeExists;
use Froq\PortalBundle\Webhook\Validator\OrganizationExists;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Webmozart\Assert\Assert as AssertProps;

final class SwitchUploadRequest
{
    public function __construct(
        #[NotBlank(message: 'EventName can not be blank.')]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly string $eventName,
        #[NotBlank(message: 'Filename can not be blank.')]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly string $filename,
        #[OrganizationExists]
        public readonly string $customerCode,
        public readonly ?string $customAssetFolder,
        #[AssetTypeExists]
        public readonly string $assetType,
        #[NotBlank(message: 'File can not be blank.')]
        #[Assert\File]
        public readonly ?UploadedFile $fileContents,
        public readonly ?string $importTagsMetadata,
        public readonly ?string $metadataFrom, // 'XMP only' or 'Filename only'
        public readonly ?string $filenameSeparator,
        #[Assert\Json(message: 'MetadataMapping is not a valid JSON')]
        public readonly ?string $metadataMapping,
        #[Assert\Json(message: 'TagsMapping is not a valid JSON')]
        public readonly ?string $tagsMapping,
        #[Assert\Json(message: 'CodeReference is not a valid JSON')]
        public readonly ?string $codeReference,
        /** @var array<int, ValidationError> $errors */
        public ?array $errors
    ) {
        AssertProps::string($this->eventName, 'Expected "eventName" to be a string, got %s');
        AssertProps::string($this->filename, 'Expected "filename" to be a string, got %s');
        AssertProps::string($this->customerCode, 'Expected "customerCode" to be a string, got %s');
        AssertProps::string($this->customAssetFolder, 'Expected "customAssetFolder" to be a string, got %s');
        AssertProps::string($this->assetType, 'Expected "assetType" to be a string, got %s');
        AssertProps::nullOrIsInstanceOf($this->fileContents, UploadedFile::class, 'Expected "fileContents" to be instance of UploadFile, got %s');
        AssertProps::string($this->importTagsMetadata, 'Expected "importTagsMetadata" to be a string, got %s');
        AssertProps::string($this->metadataFrom, 'Expected "metadataFrom" to be a string, got %s');
        AssertProps::string($this->filenameSeparator, 'Expected "filenameSeparator" to be a string, got %s');
        AssertProps::string($this->metadataMapping, 'Expected "metadataMapping" to be a string, got %s');
        AssertProps::string($this->tagsMapping, 'Expected "tagsMapping" to be a string, got %s');
        AssertProps::string($this->codeReference, 'Expected "codeReference" to be a string, got %s');
        AssertProps::allNullOrIsIterable($this->errors, 'Expected "errors" to be a array, got %s');
    }
}
