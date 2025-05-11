<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\DataTransferObject;

use Webmozart\Assert\Assert;

final class ColourGuidelineItem
{
    public function __construct(
        public int $colourGuidelineId,
        public string $name,
        public int $imageId,
        /** @var array<int, string> */
        public array $countries,
    ) {
        Assert::integer($this->colourGuidelineId, 'Expected "colourGuidelineId" to be an int, got %s');
        Assert::string($this->name, 'Expected "name" to be a string, got %s');
        Assert::integer($this->imageId, 'Expected "imageId" to be a int, got %s');
        Assert::isArray($this->countries, 'Expected "countries" to be an array, got %s');
    }
}
