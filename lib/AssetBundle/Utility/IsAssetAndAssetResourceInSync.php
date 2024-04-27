<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Utility;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\PortalBundle\Repository\AssetRepository;
use Froq\PortalBundle\Repository\AssetResourceRepository;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;

final class IsAssetAndAssetResourceInSync
{
    public function __construct(private readonly AssetRepository $assetRepository, private readonly AssetResourceRepository $assetResourceRepository)
    {
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(Organization $organization, string $filename): bool
    {
        if ($organization->getObjectFolder() === null || $organization->getAssetFolder() === null) {
            return true;
        }

        $asset = (new Asset\Listing())
            ->addConditionParam('path = ?', $organization->getAssetFolder().'/')
            ->addConditionParam('filename = ?', $filename)
            ->current();

        $assetResource = (new AssetResource\Listing())
            ->addConditionParam('o_key = ?', $filename)
            ->addConditionParam('o_path = ?', $organization->getObjectFolder().'/'.AssetResourceOrganizationFolderNames::Assets->name.'/')
            ->current();

        $assetFolderContainer = $asset === false ? null : $asset;
        $assetResourceContainer = $assetResource === false ? null : $assetResource;

        if (is_null($assetFolderContainer) && is_null($assetResourceContainer)) {
            return true;
        }

        $assetDeepestChildId = $this->assetRepository->fetchDeepestChildId((int) $assetFolderContainer?->getId());
        $assetResourceDeepestChildId = $this->assetResourceRepository->fetchDeepestChildId((int) $assetResourceContainer?->getId());

        return $asset instanceof Asset &&
            $assetResource instanceof AssetResource &&
            $assetFolderContainer instanceof Asset &&
            $assetResourceContainer instanceof AssetResource &&
            $assetFolderContainer->getChildAmount() === $assetResourceContainer->getChildAmount() &&
            (int) Asset::getById($assetDeepestChildId)?->getFilename() === (int) AssetResource::getById($assetResourceDeepestChildId)?->getKey();
    }
}
