<?php

declare(strict_types=1);

namespace Froq\AssetBundle\ValueObject;

use Webmozart\Assert\Assert;

final class MetadataFromFilename
{
    public function __construct(
        public readonly ?string $approval,
    ) {
        Assert::nullOrStringNotEmpty($this->approval, 'Expected "approval" to be a string, got %s');
    }

    /** @return  array<string, string|null> */
    public function toArray(): array
    {
        return [
            'approval' => null,
        ];
    }
}
