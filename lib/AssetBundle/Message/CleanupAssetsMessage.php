<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Message;

use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Project;
use Webmozart\Assert\Assert;

final class CleanupAssetsMessage
{
    public function __construct(
        /** @var array<int, Project> $projects */
        public readonly array $projects,
        /** @var array<int, Product> $products */
        public readonly array $products
    ) {
        Assert::isArray($this->projects, 'Expected "projects" to be an array, got %s');
        Assert::isArray($this->products, 'Expected "products" to be an array, got %s');
    }
}
