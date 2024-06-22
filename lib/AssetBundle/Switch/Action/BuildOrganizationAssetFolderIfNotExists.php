<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\Action\Processor\CreateAssetFolders;
use Pimcore\Model\DataObject\Organization ;

final class BuildOrganizationAssetFolderIfNotExists
{
    public function __construct(private readonly CreateAssetFolders $createAssetFolders)
    {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Organization $organization, string $filename): void
    {
        ($this->createAssetFolders)($organization);
    }
}
