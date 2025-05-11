<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\DataTransferObject;

use Webmozart\Assert\Assert;

final class UserGroupExportItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $key,
        public readonly string $path,
        public readonly string $name,
        /** @var array<int, UserExportItem> */
        public readonly array $users,
        /** @var array<int, string> */
        public readonly array $roles,

    ) {
        Assert::numeric($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::string($this->key, 'Expected "key" to be a string, got %s');
        Assert::string($this->path, 'Expected "path" to be a string, got %s');
        Assert::string($this->name, 'Expected "name" to be a string, got %s');
        Assert::isArray($this->users, 'Expected "users" to be aan array, got %s');
        Assert::isArray($this->roles, 'Expected "roles" to be aan array, got %s');
    }
}
