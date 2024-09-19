<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Exception;
use Froq\AssetBundle\Switch\Action\RelatedObject\CreateTagFolder;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\ValueObject\TagFromPayload;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Froq\PortalBundle\Repository\TagRepository;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Tag;

final class BuildTags
{
    public function __construct(
        private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull,
        private readonly CreateTagFolder $createTagFolder,
        private readonly TagRepository $tagRepository,
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
            $parentTagFolder = ($this->createTagFolder)($organization, $rootTagFolder);
        }

        $tagData = (array) json_decode($switchUploadRequest->tagData, true);

        if (empty($tagData) || ($this->allPropsEmptyOrNull)($tagData)) {
            return [];
        }

        $newTags = [];

        foreach ($tagData as $tagDatum) {
            if (!isset($tagDatum['code'])) {
                continue;
            }

            $tag = null;

            $tagFromPayload = new TagFromPayload(code: (string) $tagDatum['code'], name: $tagDatum['name'] ?? '');

            if ($this->tagRepository->isTagExists((string) $tagFromPayload->code)) {
                $tag = $this->tagRepository->getTagByCode((string) $tagFromPayload->code);

                if ($tag instanceof Tag) {
                    $tag->setCode($tagFromPayload->code);
                    $tag->setName($tagFromPayload->name);
                    $tag->setParentId((int) $parentTagFolder->getId());
                    $tag->setKey((string) $tagFromPayload->code);
                    $tag->setPublished(true);

                    $tag->save();
                }
            }

            if (!$this->tagRepository->isTagExists((string) $tagFromPayload->code)) {
                $tag = new Tag();

                $tag->setCode($tagFromPayload->code);
                $tag->setName($tagFromPayload->name);
                $tag->setParentId((int) $parentTagFolder->getId());
                $tag->setKey((string) $tagFromPayload->code);
                $tag->setPublished(true);

                $tag->save();
            }

            if (!($tag instanceof Tag)) {
                continue;
            }

            $newTags[] = $tag;
        }

        return array_values(array_filter(array_unique($newTags)));
    }
}
