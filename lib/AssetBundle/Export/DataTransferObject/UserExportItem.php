<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\DataTransferObject;

use Webmozart\Assert\Assert;

final class UserExportItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $foreignUserId,
        public readonly string $key,
        public readonly string $path,
        public readonly string $name,
        public readonly string $address,
        public readonly string $username,
        public readonly string $email,
        public readonly string $secondaryEmail,
        public readonly string $confirmationToken,
        public readonly string $lastLogin,
        public readonly string $passwordRequestedAt,
        public readonly ?GroupAssetLibrarySettingsExportItem $groupAssetLibrarySettingsExportItem,
        /** @var array<int, OrganizationExportItem> */
        public readonly array $organizationExportItems,
    ) {
        Assert::numeric($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::string($this->foreignUserId, 'Expected "foreignUserId" to be a string, got %s');
        Assert::string($this->key, 'Expected "key" to be a string, got %s');
        Assert::string($this->path, 'Expected "path" to be a string, got %s');
        Assert::string($this->name, 'Expected "name" to be a string, got %s');
        Assert::string($this->address, 'Expected "address" to be a string, got %s');
        Assert::string($this->username, 'Expected "username" to be a string, got %s');
        Assert::string($this->email, 'Expected "email" to be a string, got %s');
        Assert::string($this->secondaryEmail, 'Expected "secondaryEmail" to be a string, got %s');
        Assert::string($this->confirmationToken, 'Expected "confirmationToken" to be a string, got %s');
        Assert::string($this->lastLogin, 'Expected "lastLogin" to be a string, got %s');
        Assert::string($this->passwordRequestedAt, 'Expected "passwordRequestedAt" to be a string, got %s');
        Assert::isInstanceOf($this->groupAssetLibrarySettingsExportItem, GroupAssetLibrarySettingsExportItem::class, 'Expected "group" to be instance of GroupAssetLibrarySettingsExportItem, got %s');
        Assert::isArray($this->organizationExportItems, 'Expected "organizationExportItems" to be an array, got %s');
    }
}
