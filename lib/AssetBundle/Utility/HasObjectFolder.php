<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Utility;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\Organization;

final class HasObjectFolder
{
    public function __invoke(Organization $organization): bool
    {
        return (new DataObject\Listing())
            ->addConditionParam('o_key = ?', $organization->getKey())
            ->addConditionParam('o_path = ?', '/Customers/')
            ->current() instanceof Folder;
    }
}
