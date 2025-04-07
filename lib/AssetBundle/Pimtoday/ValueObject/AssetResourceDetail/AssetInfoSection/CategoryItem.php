<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection;

use Webmozart\Assert\Assert;

final class CategoryItem
{
    public function __construct(
        public string $name,
        public string $value,
        public string $link,
    ) {
        Assert::string($this->name, 'Expected "name" to be a string, got %s');
        Assert::string($this->value, 'Expected "name" to be a string, got %s');
        Assert::string($this->link, 'Expected "link" to be a string, got %s');
    }
}
