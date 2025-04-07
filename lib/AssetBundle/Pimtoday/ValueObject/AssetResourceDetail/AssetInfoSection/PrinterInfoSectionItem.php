<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection;

use Webmozart\Assert\Assert;

final class PrinterInfoSectionItem
{
    public function __construct(
        public string $name,
    ) {
        Assert::string($this->name, 'Expected "name" to be a string, got %s');
    }
}
