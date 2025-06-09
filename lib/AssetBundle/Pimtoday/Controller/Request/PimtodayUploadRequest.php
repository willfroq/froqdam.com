<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Controller\Request;

use Froq\AssetBundle\Pimtoday\Validator\IsFileBase64;
use Froq\AssetBundle\Pimtoday\ValueObject\DocumentFromPayload;
use Froq\AssetBundle\Pimtoday\ValueObject\ProductFromPayload;
use Froq\AssetBundle\Pimtoday\ValueObject\ProjectFromPayload;
use Froq\AssetBundle\Pimtoday\ValueObject\ValidationError;
use Pimcore\Model\DataObject\Organization;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

final class PimtodayUploadRequest
{
    public function __construct(
        #[NotBlank(message: 'EventName can not be blank.')]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly string $eventName,

        #[NotBlank(message: '$damOrganizationId can not be blank.')]
        public readonly int $damOrganizationId,

        public readonly ?ProjectFromPayload $projectData,

        public readonly ?DocumentFromPayload $documentData,

        public readonly ?ProductFromPayload $productData,

        #[IsFileBase64]
        public readonly ?string $fileBase64,

        public readonly ?UploadedFile $fileContents,

        public readonly ?Organization $organization,

        /** @var array<int, ValidationError> $errors */
        public ?array $errors
    ) {
    }
}
