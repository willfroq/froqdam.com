<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Message;

use Webmozart\Assert\Assert;

final class AssetResourceIdsMessage
{
    public function __construct(
        /** @var array<int, int> $parentAssetResourceIds */
        public array $parentAssetResourceIds,
        public string $newIndexName,
    ) {
        Assert::isArray($this->parentAssetResourceIds, 'Expected "parentAssetResourceIds" to be an array, got %s');
        Assert::string($this->newIndexName, 'Expected "newIndexName" to be a string, got %s');
    }
}
