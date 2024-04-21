<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Exception;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Tag;

final class BuildTags
{
    public function __construct(private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull)
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

        if (!isset($payload['tagCode'])) {
            return;
        }

        if (empty($payload) || ($this->allPropsEmptyOrNull)($payload)) {
            return;
        }

        $existingTags = $assetResource->getTags();

        $tagCodes = array_map(fn (Tag $tag) => $tag->getCode(), $existingTags);

        $newTags = [];

        foreach ($payload as $item) {
            if (empty($item)) {
                continue;
            }

            if (count((array) $item) !== 1 && is_array($item)) {
                continue;
            }

            $code = (string) array_key_first($item);
            $name = current($item);

            if (empty($name)) {
                continue;
            }

            if (in_array(needle: $code, haystack: $tagCodes)) {
                continue;
            }

            $tag = new Tag();

            $tag->setCode($code);
            $tag->setName($name);
            $tag->setParentId((int) $parentTagFolder->getId());

            $tag->save();

            $newTags[] = $tag;
        }

        $assetResource->setTags([...$newTags, ...$existingTags]);
    }
}
