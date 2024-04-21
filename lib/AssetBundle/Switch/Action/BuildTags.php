<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Exception;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Froq\PortalBundle\Repository\TagRepository;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Tag;

final class BuildTags
{
    public function __construct(private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull, private readonly TagRepository $tagRepository)
    {
    }

    /**
     * @throws Exception
     */
    public function __invoke(SwitchUploadRequest $switchUploadRequest, AssetResource $assetResource, Organization $organization): void
    {
        $rootTagFolder = $organization->getObjectFolder() . '/';

        $parentTagFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Tags->name)
            ->addConditionParam('o_path = ?', $rootTagFolder)
            ->current();

        if (!($parentTagFolder instanceof DataObject)) {
            return;
        }

        $payload = json_decode($switchUploadRequest->tagData, true);

        if (empty($payload) || ($this->allPropsEmptyOrNull)($payload)) {
            return;
        }

        $existingTags = $assetResource->getTags();

        $newTags = [];

        foreach ($payload as $item) {
            if (empty($item)) {
                continue;
            }

            if (count((array) $item) !== 2 && is_array($item)) {
                continue;
            }

            if (!isset($item['code']) || !isset($item['name'])) {
                continue;
            }

            $code = (string) $item['code'];
            $name = (string) $item['name'];

            if ($this->tagRepository->isTagExists($organization, $code)) {
                continue;
            }

            $tag = new Tag();

            $tag->setCode($code);
            $tag->setName($name);
            $tag->setParentId((int) $parentTagFolder->getId());
            $tag->setKey($code);
            $tag->setPublished(true);

            $tag->save();

            $newTags[] = $tag;
        }

        $assetResource->setTags([...$newTags, ...$existingTags]);
    }
}
