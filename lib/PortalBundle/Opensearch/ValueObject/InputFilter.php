<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\ValueObject;

use Webmozart\Assert\Assert;

final class InputFilter
{
    public function __construct(
        public string $filterName,

        public readonly string $text,
    ) {
        Assert::string($this->filterName, 'Expected "filterName" to be a string, got %s');
        Assert::string($this->text, 'Expected "text" to be a string, got %s');
    }
}
