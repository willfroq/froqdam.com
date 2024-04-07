<?php

declare(strict_types=1);

namespace Froq\AssetBundle\ValueObject;

use Webmozart\Assert\Assert;

final class MetadataMapping
{
    public function __construct(
        public ?string $brand = null,
        public ?string $inhoud = null,
        public ?string $shape = null,
        public ?string $verpakking = null,
        public ?string $packshottype = null,
    ) {
        Assert::nullOrString($this->brand, 'Expected "brand" to be a string, got %s');
        Assert::nullOrString($this->inhoud, 'Expected "inhoud" to be a string, got %s');
        Assert::nullOrString($this->shape, 'Expected "shape" to be a string, got %s');
        Assert::nullOrString($this->verpakking, 'Expected "verpakking" to be a string, got %s');
        Assert::nullOrString($this->packshottype, 'Expected "packshottype" to be a string, got %s');
    }
}
