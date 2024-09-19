<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Repository;

use Pimcore\Model\DataObject\Tag;

final class TagRepository
{
    public function getTagByCode(string $code): ?Tag
    {
        $tag = (new Tag\Listing())
            ->addConditionParam('Code = ?', $code)
            ->current();

        if (!($tag instanceof Tag)) {
            return null;
        }

        return $tag;
    }

    public function isTagExists(string $code): bool
    {
        $tag = (new Tag\Listing())
            ->addConditionParam('Code = ?', $code)
            ->current();

        if (!($tag instanceof Tag)) {
            return false;
        }

        return true;
    }
}
