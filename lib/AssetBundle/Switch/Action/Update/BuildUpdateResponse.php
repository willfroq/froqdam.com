<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\Update;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Action\RelatedObject\CreateTagFolder;
use Froq\AssetBundle\Switch\Controller\Request\UpdateRequest;
use Froq\AssetBundle\Switch\Controller\Request\UpdateResponse;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\ValueObject\TagFromPayload;
use Froq\PortalBundle\Repository\TagRepository;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\AssetResourceMetadata;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Tag;

final class BuildUpdateResponse
{
    public function __construct(
        private readonly TagRepository $tagRepository,
        private readonly BuildProductFromPayload $buildProductFromPayload,
        private readonly BuildProjectFromPayload $buildProjectFromPayload,
        private readonly CreateTagFolder $createTagFolder,
    ) {
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function __invoke(UpdateRequest $updateRequest): UpdateResponse
    {
        $parentAssetResource = AssetResource::getById($updateRequest->parentAssetResourceId);

        if (!($parentAssetResource instanceof AssetResource)) {
            throw new \Exception(message: 'ParentAssetResource does not exists.');
        }

        $children = $parentAssetResource->getChildren();

        $latestAssetResourceVersion = end($children);

        if (!($latestAssetResourceVersion instanceof AssetResource)) {
            throw new \Exception(message: 'ParentAssetResource should have a version!');
        }

        $this->updateMetadata($updateRequest, $latestAssetResourceVersion);

        $this->updateTags($updateRequest, $latestAssetResourceVersion);

        ($this->buildProductFromPayload)($updateRequest, $parentAssetResource);
        ($this->buildProjectFromPayload)($updateRequest, $parentAssetResource);

        $parentAssetResource->setPublished(true);

        return new UpdateResponse(
            eventName: 'update',
            date: date('F j, Y H:i'),
            filename: $updateRequest->filename,
            parentAssetResourceId: (int) $parentAssetResource->getId(),
            latestAssetResourceId: (int) $latestAssetResourceVersion->getId(),
            status: 200,
            message: sprintf('ParentAssetResource %s and LatestAssetResourceVersion %s are updated!', $parentAssetResource->getId(), $latestAssetResourceVersion->getId()),
            actions: [],
        );
    }

    private function updateMetadata(UpdateRequest $updateRequest, AssetResource $latestAssetResourceVersion): void
    {
        $metadata = (array) json_decode($updateRequest->assetResourceMetadataFieldCollection, true);

        $fieldCollectionItems = [];

        if (!empty($metadata)) {
            foreach ($metadata as $item) {
                if (count((array) $item) !== 1) {
                    continue;
                }

                $assetResourceMetadata = new AssetResourceMetadata();

                $assetResourceMetadata->setMetadataKey((string) array_key_first($item));
                $assetResourceMetadata->setMetadataValue(current($item));

                $fieldCollectionItems[] = $assetResourceMetadata;
            }

            $fieldCollection = new Fieldcollection();
            $fieldCollection->setItems($fieldCollectionItems);

            $latestAssetResourceVersion->setMetadata($fieldCollection);
        }
    }

    /**
     * @throws \Exception
     */
    private function updateTags(UpdateRequest $updateRequest, AssetResource $latestAssetResourceVersion): void
    {
        $rootTagFolder = $updateRequest->parentAssetResourceFolderPath;

        $organization = Organization::getById($updateRequest->organizationId);

        if (!($organization instanceof Organization)) {
            throw new \Exception(message: 'Organization does not exists.');
        }

        $tagData = (array) json_decode($updateRequest->tagData, true);

        $newTags = [];

        if (!empty($tagData)) {
            $parentTagFolder = (new DataObject\Listing())
                ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Tags->name)
                ->addConditionParam('o_path = ?', $rootTagFolder)
                ->current();

            if (!($parentTagFolder instanceof DataObject)) {
                $parentTagFolder = ($this->createTagFolder)($organization, $rootTagFolder);
            }

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
        }

        $latestAssetResourceVersion->setTags($newTags);
    }
}
