<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject;

use Webmozart\Assert\Assert;

final class ValidationError
{
    public function __construct(
        public readonly string $propertyPath,
        public readonly string $message,
    ) {
        Assert::string($this->propertyPath, 'Expected "propertyPath" to be a string, got %s');
        Assert::string($this->message, 'Expected "message" to be a string, got %s');
    }
}
