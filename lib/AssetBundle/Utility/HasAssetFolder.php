<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Utility;

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Organization;

final class HasAssetFolder
{
    public function __invoke(Organization $organization, string $filename): bool
    {
        return (new Asset\Listing())
            ->addConditionParam('path = ?', $organization->getAssetFolder().'/')
            ->addConditionParam('filename = ?', $filename)
            ->current() instanceof Asset;
    }
}
