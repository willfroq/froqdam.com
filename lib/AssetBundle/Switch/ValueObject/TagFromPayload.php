<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\ValueObject;

use Webmozart\Assert\Assert;

final class TagFromPayload
{
    public function __construct(
        public readonly ?string $code,
        public readonly ?string $name,
    ) {
        Assert::nullOrString($this->code, 'Expected "code" to be a string, got %s');
        Assert::nullOrString($this->name, 'Expected "name" to be a string, got %s');
    }
}
