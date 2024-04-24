<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\ValueObject;

use Webmozart\Assert\Assert;

final class ValidatedRequest
{
    public function __construct(
        public readonly ?string $brand,
    ) {
        Assert::nullOrString($this->brand, 'Expected "brand" to be a string, got %s');
    }
}
