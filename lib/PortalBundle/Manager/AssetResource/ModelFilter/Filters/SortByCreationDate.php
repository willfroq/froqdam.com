<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\AssetResource\ModelFilter\Filters;

use Froq\PortalBundle\Manager\AssetResource\ModelFilter\Contract\FilterInterface;
use Pimcore\Model\DataObject\AssetResource;

class SortByCreationDate implements FilterInterface
{
    public function supports(mixed $value): bool
    {
        return !empty($value);
    }

    public function apply(AssetResource\Listing $listing, mixed $value): AssetResource\Listing
    {
        $listing->setOrderKey('o_creationDate');
        $listing->setOrder($value);

        return $listing;
    }
}
