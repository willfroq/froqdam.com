<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\AssetResource\ModelFilter\Contract;

use Pimcore\Model\DataObject\AssetResource;

interface FilterInterface
{
    public function supports(mixed $value): bool;

    public function apply(AssetResource\Listing $listing, mixed $value): AssetResource\Listing;
}
