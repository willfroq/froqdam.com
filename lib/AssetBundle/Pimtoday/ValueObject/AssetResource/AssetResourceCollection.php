<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject\AssetResource;

use Froq\AssetBundle\Pimtoday\ValueObject\Filters\Date;
use Froq\AssetBundle\Pimtoday\ValueObject\Filters\Input;
use Froq\AssetBundle\Pimtoday\ValueObject\Filters\MulticheckboxCollection;
use Froq\AssetBundle\Pimtoday\ValueObject\Filters\Range;
use Webmozart\Assert\Assert;

final class AssetResourceCollection
{
    public function __construct(
        public int $totalCount,
        public int $size,
        public int $page,

        /** @var array<int, MulticheckboxCollection> */
        public array $multicheckboxes,
        /** @var array<int, Input> */
        public array $inputs,
        /** @var array<int, Date> */
        public array $dates,
        /** @var array<int, Range> */
        public array $ranges,

        /** @var array<int, AssetResourceItem> */
        public array $items,
    ) {
        Assert::integer($this->totalCount, 'Expected "totalCount" to be a numeric, got %s');
        Assert::integer($this->size, 'Expected "size" to be a numeric, got %s');
        Assert::integer($this->page, 'Expected "page" to be a numeric, got %s');

        Assert::isArray($this->multicheckboxes, 'Expected "multicheckbox" to be an instance of MulticheckboxCollection, got %s');
        Assert::isArray($this->inputs, 'Expected "input" to be an instance of Input, got %s');
        Assert::isArray($this->dates, 'Expected "date" to be an instance of Date, got %s');
        Assert::isArray($this->ranges, 'Expected "range" to be an instance of Range, got %s');

        Assert::isArray($this->items, 'Expected "items" to be an array, got %s');
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'totalCount' => $this->totalCount,
            'size' => $this->size,
            'page' => $this->page,

            'multicheckboxes' => $this->multicheckboxes,
            'inputs' => $this->inputs,
            'dates' => $this->dates,
            'ranges' => $this->ranges,

            'items' => $this->items,
        ];
    }
}
