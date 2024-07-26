<?php

declare(strict_types=1);

namespace Froq\AssetBundle\ValueObject;

use Webmozart\Assert\Assert;

final class FileDate
{
    public function __construct(
        public readonly ?string $createDate,
        public readonly ?string $modifyDate,
    ) {
        Assert::string($this->createDate, 'Expected "createDate" to be a string, got %s');
        Assert::string($this->modifyDate, 'Expected "modifyDate" to be a string, got %s');
    }
}
