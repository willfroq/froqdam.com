<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\ValueObject;

use Webmozart\Assert\Assert;

final class ProjectFromPayload
{
    public function __construct(
        public readonly ?string $projectCode,
        public readonly ?string $projectName,
        public readonly ?string $pimProjectNumber,
        public readonly ?string $froqProjectNumber,
        public readonly ?string $customerProjectNumber,
        public readonly ?string $froqName,
        public readonly ?string $description,
        public readonly ?string $projectType,
        public readonly ?string $status,
        public readonly ?string $location,
        public readonly ?string $deliveryType,
    ) {
        Assert::nullOrString($this->projectCode, 'Expected "projectCode" to be a string, got %s');
        Assert::nullOrString($this->projectName, 'Expected "projectName" to be a string, got %s');
        Assert::nullOrString($this->pimProjectNumber, 'Expected "pimProjectNumber" to be a string, got %s');
        Assert::nullOrString($this->froqProjectNumber, 'Expected "froqProjectNumber" to be a string, got %s');
        Assert::nullOrString($this->customerProjectNumber, 'Expected "customerProjectNumber" to be a string, got %s');
        Assert::nullOrString($this->froqName, 'Expected "froqName" to be a string, got %s');
        Assert::nullOrString($this->description, 'Expected "description" to be a string, got %s');
        Assert::nullOrString($this->projectType, 'Expected "projectType" to be a string, got %s');
        Assert::nullOrString($this->status, 'Expected "status" to be a string, got %s');
        Assert::nullOrString($this->location, 'Expected "location" to be a string, got %s');
        Assert::nullOrString($this->deliveryType, 'Expected "deliveryType" to be a string, got %s');
    }
}
