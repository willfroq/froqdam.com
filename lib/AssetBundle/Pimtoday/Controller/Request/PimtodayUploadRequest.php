<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Controller\Request;

use Froq\AssetBundle\Pimtoday\Validator\IsFileBase64;
use Froq\AssetBundle\Pimtoday\ValueObject\DocumentFromPayload;
use Froq\AssetBundle\Pimtoday\ValueObject\ProjectFromPayload;
use Froq\PortalBundle\Api\ValueObject\ValidationError;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Webmozart\Assert\Assert as AssertProps;

final class PimtodayUploadRequest
{
    public function __construct(
        #[NotBlank(message: 'EventName can not be blank.')]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly string $eventName,

        public readonly ProjectFromPayload $projectData,

        public readonly DocumentFromPayload $documentData,

        #[NotBlank(message: 'File can not be blank.')]
        #[IsFileBase64]
        public readonly string $file,

        /** @var array<int, ValidationError> $errors */
        public ?array $errors
    ) {
        AssertProps::string($this->eventName, 'Expected "eventName" to be a string, got %s');
        AssertProps::isInstanceOf($this->projectData, ProjectFromPayload::class, 'Expected "projectData" to be instance of ProjectFromPayload, got %s');
        AssertProps::isInstanceOf($this->documentData, DocumentFromPayload::class, 'Expected "documentData" to be instance of DocumentFromPayload, got %s');
        AssertProps::string($this->file, 'Expected "file" to be a string, got %s');
        AssertProps::allNullOrIsIterable($this->errors, 'Expected "errors" to be a array, got %s');
    }
}
