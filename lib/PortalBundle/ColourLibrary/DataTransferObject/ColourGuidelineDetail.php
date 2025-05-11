<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\DataTransferObject;

use Webmozart\Assert\Assert;

final class ColourGuidelineDetail
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,

    ) {
        Assert::integer($this->id, 'Expected "colourGuidelineId" to be an int, got %s');
        Assert::string($this->name, 'Expected "name" to be a string, got %s');
    }
}
