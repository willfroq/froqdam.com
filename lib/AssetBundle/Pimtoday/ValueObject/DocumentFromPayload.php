<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;
use Webmozart\Assert\Assert as AssertProps;

final class DocumentFromPayload
{
    public function __construct(
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly int $documentIdentifier,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly string $documentName,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly string $documentType,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly string $documentFileType,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly string $documentExtension,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly string $documentStatus,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly string $documentSku,

        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly int $documentSkuId,

        #[Assert\Type(type: 'boolean', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly bool $documentIsProcessed,

        #[Assert\Type(type: 'boolean', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly bool $documentIsSentToSftp,

        #[Assert\Type(type: 'boolean', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly bool $documentIsReplaced,
    ) {
        AssertProps::integer($this->documentIdentifier, 'Expected "projectNumber" to be a integer, got %s');
        AssertProps::string($this->documentName, 'Expected "documentName" to be a string, got %s');
        AssertProps::string($this->documentType, 'Expected "documentType" to be a string, got %s');
        AssertProps::string($this->documentFileType, 'Expected "documentFileType" to be a string, got %s');
        AssertProps::string($this->documentExtension, 'Expected "documentExtension" to be a string, got %s');
        AssertProps::string($this->documentStatus, 'Expected "documentStatus" to be a string, got %s');
        AssertProps::string($this->documentSku, 'Expected "documentSku" to be a string, got %s');
        AssertProps::integer($this->documentSkuId, 'Expected "documentSkuId" to be a integer, got %s');
        AssertProps::boolean($this->documentIsProcessed, 'Expected "documentIsProcessed" to be a boolean, got %s');
        AssertProps::boolean($this->documentIsSentToSftp, 'Expected "documentIsSentToSftp" to be a boolean, got %s');
        AssertProps::boolean($this->documentIsReplaced, 'Expected "documentIsReplaced" to be a boolean, got %s');
    }
}
