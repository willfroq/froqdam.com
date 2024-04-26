<?php

namespace Froq\AssetBundle\Utility;

use Pimcore\Model\DataObject\Tag;

final class IsTagExists
{
    public function __invoke(string $column, string $value): bool
    {
        return match ($column) {
            'Code' => (fn () => (new Tag\Listing())
                    ->addConditionParam('Code = ?', $value)
                    ->current() instanceof Tag)(),
            default => false
        };
    }
}
