<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\DataTransferObject;

use Webmozart\Assert\Assert;

final class ClientExportCollection
{
    public function __construct(
        /** @var array<int, UserExportItem> */
        public readonly array $userExportItems,
        /** @var array<int, UserGroupExportItem> */
        public readonly array $userGroupExportItems,
    ) {
        Assert::isArray($this->userExportItems, 'Expected "userExportItems" to be an array, got %s');
        Assert::isArray($this->userGroupExportItems, 'Expected "userGroupExportItems" to be an array, got %s');
    }
}
