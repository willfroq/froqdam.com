<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\Search;

use Webmozart\Assert\Assert;

final class InputFilter
{
    public function __construct(
        public string $text,
    ) {
        Assert::string($this->text, 'Expected "text" to be a string, got %s');
    }
}
