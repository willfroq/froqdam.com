<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

final class DocumentFromPayload
{
    public function __construct(
        #[NotBlank(message: '$pimTodayId can not be blank.')]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly int $pimTodayId,

        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public ?int $damId,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $documentIdentifier,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $damFilename,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $damVersion,

        #[NotBlank(message: '$pimTodayId can not be blank.')]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $documentName,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $documentType,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $documentFileType,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $documentExtension,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $documentStatus,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $documentSku,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $documentSkuId,

        #[Assert\Type(type: 'boolean', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?bool $documentIsProcessed,

        #[Assert\Type(type: 'boolean', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?bool $documentIsSentToSftp,

        #[Assert\Type(type: 'boolean', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?bool $documentIsReplaced,
    ) {
    }
}
