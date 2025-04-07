<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Controller\Request;

use Froq\AssetBundle\Pimtoday\ValueObject\ValidationError;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

final class FileRequest
{
    public function __construct(
        #[NotBlank(message: '$assetResourceId can not be blank.')]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?int $assetResourceId,

        /** @var array<int, ValidationError> $errors */
        public ?array $errors
    ) {
    }
}
