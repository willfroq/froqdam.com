<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Controller\Request;

use Froq\AssetBundle\Switch\Validator\AssetTypeExists;
use Froq\AssetBundle\Switch\Validator\CustomerAssetFolderExists;
use Froq\AssetBundle\Switch\Validator\IsFilename;
use Froq\AssetBundle\Switch\Validator\IsJsonMaxOneLevelArray;
use Froq\AssetBundle\Switch\Validator\IsJsonMaxThreeLevelsDeep;
use Froq\AssetBundle\Switch\Validator\OrganizationExists;
use Froq\AssetBundle\Switch\Validator\OrganizationKeyAndNameMustBeEqual;
use Froq\AssetBundle\Switch\ValueObject\ValidationError;
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
        #[IsFilename]
        public readonly string $filename,
        #[NotBlank(message: 'CustomerCode can not be blank.')]
        #[OrganizationExists]
        #[OrganizationKeyAndNameMustBeEqual]
        public readonly string $customerCode,
        #[CustomerAssetFolderExists]
        public readonly ?string $customAssetFolder,
        #[NotBlank(message: 'AssetType can not be blank.')]
        #[AssetTypeExists]
        public readonly string $assetType,
        #[NotBlank(message: 'File can not be blank.')]
        #[Assert\File]
        public readonly ?UploadedFile $fileContents,
        #[NotBlank(message: 'AssetResourceValidFrom can not be blank.')]
        #[Assert\Json(message: 'AssetResourceMetadataFieldCollection is not a valid JSON')]
        #[IsJsonMaxOneLevelArray]
        public readonly string $assetResourceMetadataFieldCollection,
        #[Assert\Json(message: 'ProductData is not a valid JSON')]
        #[IsJsonMaxThreeLevelsDeep]
        public readonly string $productData,
        #[Assert\Json(message: 'TagData is not a valid JSON')]
        #[IsJsonMaxOneLevelArray]
        public readonly string $tagData,
        #[Assert\Json(message: 'ProjectData is not a valid JSON')]
        #[IsJsonMaxThreeLevelsDeep]
        public readonly string $projectData,
        #[Assert\Json(message: 'PrinterData is not a valid JSON')]
        #[IsJsonMaxThreeLevelsDeep]
        public readonly string $printerData,
        #[Assert\Json(message: 'SupplierData is not a valid JSON')]
        #[IsJsonMaxThreeLevelsDeep]
        public readonly string $supplierData,
        /** @var array<int, ValidationError> $errors */
        public ?array $errors
    ) {
        AssertProps::string($this->eventName, 'Expected "eventName" to be a string, got %s');
        AssertProps::string($this->filename, 'Expected "filename" to be a string, got %s');
        AssertProps::string($this->customerCode, 'Expected "customerCode" to be a string, got %s');
        AssertProps::nullOrString($this->customAssetFolder, 'Expected "customAssetFolder" to be a string, got %s');
        AssertProps::string($this->assetType, 'Expected "assetType" to be a string, got %s');
        AssertProps::nullOrIsInstanceOf($this->fileContents, UploadedFile::class, 'Expected "fileContents" to be instance of UploadFile, got %s');
        AssertProps::string($this->assetResourceMetadataFieldCollection, 'Expected "assetResourceMetadataFieldCollection" to be a string, got %s');
        AssertProps::string($this->productData, 'Expected "productData" to be a string, got %s');
        AssertProps::string($this->tagData, 'Expected "tagData" to be a string, got %s');
        AssertProps::string($this->projectData, 'Expected "projectData" to be a string, got %s');
        AssertProps::string($this->printerData, 'Expected "printerData" to be a string, got %s');
        AssertProps::string($this->supplierData, 'Expected "supplierData" to be a string, got %s');
        AssertProps::allNullOrIsIterable($this->errors, 'Expected "errors" to be a array, got %s');
    }
}
