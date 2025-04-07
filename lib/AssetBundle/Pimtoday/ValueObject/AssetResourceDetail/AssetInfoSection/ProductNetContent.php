<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection;

use Webmozart\Assert\Assert;

final class ProductNetContent
{
    public function __construct(
        public readonly string $attribute,
        public readonly string $value,
        public readonly string $link,
    ) {
        Assert::string($this->attribute, 'Expected "attribute" to be a string, got %s');
        Assert::string($this->value, 'Expected "value" to be a string, got %s');
        Assert::string($this->link, 'Expected "link" to be a string, got %s');
    }
}
