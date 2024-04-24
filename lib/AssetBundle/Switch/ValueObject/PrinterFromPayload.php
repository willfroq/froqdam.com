<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\ValueObject;

use Webmozart\Assert\Assert;

final class PrinterFromPayload
{
    public function __construct(
        public readonly ?string $printingProcess,
        public readonly ?string $printingWorkflow,
        public readonly ?string $epsonMaterial,
        public readonly ?string $substrateMaterial,
    ) {
        Assert::nullOrString($this->printingProcess, 'Expected "printingProcess" to be a string, got %s');
        Assert::nullOrString($this->printingWorkflow, 'Expected "printingWorkflow" to be a string, got %s');
        Assert::nullOrString($this->epsonMaterial, 'Expected "epsonMaterial" to be a string, got %s');
        Assert::nullOrString($this->substrateMaterial, 'Expected "substrateMaterial" to be a string, got %s');
    }
}
