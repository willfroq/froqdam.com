<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\AssetResourceDetail\AssetInfoSection;

use Webmozart\Assert\Assert;

final class PrinterInfoSectionCollection
{
    public function __construct(
        public int $totalCount,

        /** @var array<int, PrinterInfoSectionItem> */
        public array $items = [],
    ) {
        Assert::numeric($this->totalCount, 'Expected "totalCount" to be a numeric, got %s');
        Assert::isArray($this->items, 'Expected "items" to be a array, got %s');
    }
}
