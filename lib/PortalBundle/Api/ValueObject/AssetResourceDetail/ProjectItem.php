<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\ValueObject\AssetResourceDetail;

use Webmozart\Assert\Assert;

final class ProjectItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $categoryManagersTableRowLabel,
        public readonly string $categoryManagers,
        public readonly string $projectNameTableRowLabel,
        public readonly string $projectName,
        public readonly string $projectNameLink,
        public readonly string $pimProjectNumberTableRowLabel,
        public readonly string $pimProjectNumber,
        public readonly string $pimProjectNumberLink,
        public readonly string $froqProjectNumberTableRowLabel,
        public readonly string $froqProjectNumber,
        public readonly string $froqProjectNumberLink,
        public readonly string $customerTableRowLabel,
        public readonly string $customerName,
        public readonly string $customerNameLink,
        public readonly string $froqProjectNameTableRowLabel,
        public readonly string $froqProjectName,
        public readonly string $froqProjectNameLink,
    ) {
        Assert::numeric($this->id, 'Expected "id" to be a numeric, got %s');
        Assert::string($this->categoryManagersTableRowLabel, 'Expected "categoryManagersTableRowLabel" to be a string, got %s');
        Assert::string($this->categoryManagers, 'Expected "categoryManagers" to be a string, got %s');
        Assert::string($this->projectNameTableRowLabel, 'Expected "projectNameTableRowLabel" to be a string, got %s');
        Assert::string($this->projectName, 'Expected "projectName" to be a string, got %s');
        Assert::string($this->projectNameLink, 'Expected "projectNameLink" to be a string, got %s');
        Assert::string($this->pimProjectNumberTableRowLabel, 'Expected "pimProjectNumberTableRowLabel" to be a string, got %s');
        Assert::string($this->pimProjectNumber, 'Expected "pimProjectNumber" to be a string, got %s');
        Assert::string($this->pimProjectNumberLink, 'Expected "pimProjectNumberLink" to be a string, got %s');
        Assert::string($this->froqProjectNumberTableRowLabel, 'Expected "froqProjectNumberTableRowLabel" to be a string, got %s');
        Assert::string($this->froqProjectNumber, 'Expected "froqProjectNumber" to be a string, got %s');
        Assert::string($this->froqProjectNumberLink, 'Expected "froqProjectNumberLink" to be a string, got %s');
        Assert::string($this->customerTableRowLabel, 'Expected "customerTableRowLabel" to be a string, got %s');
        Assert::string($this->customerName, 'Expected "customerName" to be a string, got %s');
        Assert::string($this->customerNameLink, 'Expected "customerNameLink" to be a string, got %s');
        Assert::string($this->froqProjectNameTableRowLabel, 'Expected "froqProjectNameTableRowLabel" to be a string, got %s');
        Assert::string($this->froqProjectName, 'Expected "froqProjectName" to be a string, got %s');
        Assert::string($this->froqProjectNameLink, 'Expected "froqProjectNameLink" to be a string, got %s');
    }
}
