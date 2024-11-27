<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Message;

use Webmozart\Assert\Assert;

final class FillInAssetResourceFileCreatedAndModifiedDateMessage
{
    public function __construct(
        /** @var array<int, int> $parentIds */
        public readonly array $parentIds
    ) {
        Assert::isArray($this->parentIds, 'Expected "parentIds" to be an array, got %s');
    }
}
