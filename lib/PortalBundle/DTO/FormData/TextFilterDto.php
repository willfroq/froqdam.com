<?php

declare(strict_types=1);

namespace Froq\PortalBundle\DTO\FormData;

use Webmozart\Assert\Assert;

class TextFilterDto
{
    public function __construct(
        public readonly string $field,
        public readonly string $searchTerm,
    ) {
        Assert::string($this->field, 'Expected "field" to be a string, got %s');
        Assert::string($this->searchTerm, 'Expected "searchTerm" to be a string, got %s');
    }
}
