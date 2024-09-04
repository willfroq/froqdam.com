<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Repository;

use Pimcore\Model\DataObject\Tag;

final class TagRepository
{
    /** @param array<int, Tag> $existingTags */
    public function isPayloadTagCodeExistsInExistingTags(array $existingTags, string $tagCode): bool
    {
        foreach ($existingTags as $tag) {
            if ($tag->getCode() === $tagCode) {
                return true;
            }
        }

        return false;
    }

    /** @param array<int, Tag> $existingTags */
    public function getTagFromExistingTags(array $existingTags, string $tagCode): ?Tag
    {
        foreach ($existingTags as $tag) {
            if ($tag->getCode() === $tagCode) {
                return $tag;
            }
        }

        return null;
    }
}
