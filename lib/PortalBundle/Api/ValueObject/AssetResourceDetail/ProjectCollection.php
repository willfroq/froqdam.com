<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\ValueObject\AssetResourceDetail;

use Webmozart\Assert\Assert;

final class ProjectCollection
{
    public function __construct(
        public readonly int $totalCount,
        public readonly string $assetDetailConfigLabel,

        /** @var array<int, ProjectItem> */
        public readonly array $items,
    ) {
        Assert::numeric($this->totalCount, 'Expected "totalCount" to be a numeric, got %s');
        Assert::string($this->assetDetailConfigLabel, 'Expected "assetDetailConfigLabel" to be an string, got %s');
        Assert::isArray($this->items, 'Expected "projects" to be an array, got %s');
    }
}
