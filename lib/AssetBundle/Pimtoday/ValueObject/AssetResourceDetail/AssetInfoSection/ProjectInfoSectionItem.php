<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection;

use Webmozart\Assert\Assert;

final class ProjectInfoSectionItem
{
    public function __construct(
        public string $pimProjectName,
        public string $froqName,
        public string $froqNumber,

    ) {
        Assert::string($this->pimProjectName, 'Expected "pimProjectName" to be a string, got %s');
        Assert::string($this->froqName, 'Expected "froqName" to be a string, got %s');
        Assert::string($this->froqNumber, 'Expected "froqNumber" to be a string, got %s');
    }
}
