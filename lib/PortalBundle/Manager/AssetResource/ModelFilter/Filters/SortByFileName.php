<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\AssetResource\ModelFilter\Filters;

use Doctrine\DBAL\Query\QueryBuilder;
use Froq\PortalBundle\Manager\AssetResource\ModelFilter\Contract\FilterInterface;
use Pimcore\Model\DataObject\AssetResource;
use Youwe\CommonBundle\Service\ListingService;

class SortByFileName implements FilterInterface
{
    public function __construct(private readonly ListingService $listingService)
    {
    }

    public function supports(mixed $value): bool
    {
        return !empty($value);
    }

    public function apply(AssetResource\Listing $listing, mixed $value): AssetResource\Listing
    {
        $this->listingService->onCreateQueryBuilder($listing, function (QueryBuilder $query) {
            $query->leftJoin('object_AssetResource', 'assets', 'aT',
                'object_AssetResource.Asset__id = aT.id'
            );
        });

        $listing->setOrderKey('aT.filename');
        $listing->setOrder($value);

        return $listing;
    }
}
