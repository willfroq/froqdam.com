<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\ValueObject;

use Webmozart\Assert\Assert;

final class CustomerOrganization
{
    public function __construct(
        public readonly ?string $organizationCode,
        public readonly ?string $organization,
        public readonly ?string $codeField,
        public readonly ?string $pattern,
    ) {
        Assert::nullOrString($this->organizationCode, 'Expected "organizationCode" to be a string, got %s');
        Assert::nullOrString($this->organization, 'Expected "organization" to be a string, got %s');
        Assert::nullOrString($this->codeField, 'Expected "codeField" to be a string, got %s');
        Assert::nullOrString($this->pattern, 'Expected "pattern" to be a string, got %s');
    }
}
