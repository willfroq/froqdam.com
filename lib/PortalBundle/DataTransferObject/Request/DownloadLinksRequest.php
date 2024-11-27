<?php

declare(strict_types=1);

namespace Froq\PortalBundle\DataTransferObject\Request;

use Froq\PortalBundle\Api\ValueObject\ValidationError;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Validator\Constraints\NotBlank;
use Webmozart\Assert\Assert as AssertProps;

final class DownloadLinksRequest
{
    public function __construct(/** @phpstan-ignore-line */
        #[NotBlank(message: 'AssetResourceIds can not be blank.')]
        /** @var array<int, int> $assetResourceIds */
        public readonly array $assetResourceIds,
        public readonly User $user,
        /** @var array<int, ValidationError> $errors */
        public ?array $errors
    ) {
        AssertProps::isArray($this->assetResourceIds, 'Expected "assetResourceIds" to be an array, got %s');
        AssertProps::isInstanceOf($this->user, User::class, 'Expected "user" to be an instance of User, got %s');
        AssertProps::allNullOrIsIterable($this->errors, 'Expected "errors" to be a array, got %s');
    }
}
