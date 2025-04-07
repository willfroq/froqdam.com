<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection;

use Webmozart\Assert\Assert;

final class TagItem
{
    public function __construct(
        public string $name,
        public string $link,
    ) {
        Assert::string($this->name, 'Expected "name" to be a string, got %s');
        Assert::string($this->link, 'Expected "link" to be a string, got %s');
    }
}
