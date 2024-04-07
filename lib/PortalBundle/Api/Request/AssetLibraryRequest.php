<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Request;

use Pimcore\Model\DataObject\User;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;

final class AssetLibraryRequest
{
    public function __construct(
        #[NotBlank(message: 'User can not be blank.')]
        public ?User $user,

        #[IsTrue(message: 'User does not belong to an Organization.')]
        public bool $hasOrganization = false,

        public ?string $query = null,
        public ?string $page = null,
        public ?string $size = null,

        /**
         * @var array<string|int, mixed>
         */
        public array $filters = [],
        public ?string $sort_by = null,
        public ?string $sort_direction = null
    ) {

    }
}
