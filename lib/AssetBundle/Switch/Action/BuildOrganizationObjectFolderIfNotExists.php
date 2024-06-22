<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\Action\Processor\CreateAssetResourceFolder;
use Pimcore\Model\DataObject\Organization;

final class BuildOrganizationObjectFolderIfNotExists
{
    public function __construct(private readonly CreateAssetResourceFolder $createAssetResourceFolder)
    {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Organization $organization, string $customAssetFolder): void
    {
        ($this->createAssetResourceFolder)($organization, $customAssetFolder);
    }
}
