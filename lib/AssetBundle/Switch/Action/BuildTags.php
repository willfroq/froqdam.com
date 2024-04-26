<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Exception;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\ValueObject\TagFromPayload;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Froq\AssetBundle\Utility\IsPathExists;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Tag;

final class BuildTags
{
    public function __construct(
        private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull,
        private readonly IsPathExists $isPathExists,
    ) {
    }

    /**
     * @throws Exception
     *
     * @param array<int, string> $actions
     *
     * @return array<int, Tag>
     */
    public function __invoke(
        SwitchUploadRequest $switchUploadRequest,
        Organization $organization,
        array $actions
    ): array {
        $rootTagFolder = $organization->getObjectFolder() . '/';

        $parentTagFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Tags->name)
            ->addConditionParam('o_path = ?', $rootTagFolder)
            ->current();

        if (!($parentTagFolder instanceof DataObject)) {
            return [];
        }

        $tagData = (array) json_decode($switchUploadRequest->tagData, true);

        if (empty($tagData) || ($this->allPropsEmptyOrNull)($tagData)) {
            return [];
        }

        $tagPath = $rootTagFolder.AssetResourceOrganizationFolderNames::Tags->name.'/';

        $newTags = [];

        foreach ($tagData as $tagDatum) {
            $tagFromPayload = new TagFromPayload(code: $tagDatum['code'] ?? null, name: $tagDatum['name'] ?? null);

            if (!($this->isPathExists)((string) $tagFromPayload->code, $tagPath)) {
                $tag = new Tag();

                $tag->setCode($tagFromPayload->code);
                $tag->setName($tagFromPayload->name);
                $tag->setParentId((int) $parentTagFolder->getId());
                $tag->setKey((string) $tagFromPayload->code);
                $tag->setPublished(true);

                $tag->save();

                $newTags[] = $tag;
            }
        }

        return $newTags;
    }
}
