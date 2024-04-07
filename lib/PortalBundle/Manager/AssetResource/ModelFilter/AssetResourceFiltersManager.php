<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\AssetResource\ModelFilter;

use App\Kernel as App;
use Froq\PortalBundle\Helper\StrHelper;
use Froq\PortalBundle\Manager\AssetResource\ModelFilter\Contract\FilterInterface;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AssetResourceFiltersManager
{
    private readonly Request|null $request;

    public function __construct(RequestStack $requestStack, private readonly App $app)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function apply(AssetResource\Listing $listing): AssetResource\Listing
    {
        $listing = $this->applySorting($listing);
        $listing = $this->applyFilters($listing);

        return $listing;
    }

    protected function applySorting(AssetResource\Listing $listing): AssetResource\Listing
    {
        $sortKey = $this->request?->get('sort_by', 'file_name');

        $className = 'SortBy'.StrHelper::snakeToPascal($sortKey);

        $sortDirection = $this->request?->get('sort_direction', 'asc');

        return $this->process($listing, $className, $sortDirection);
    }

    protected function applyFilters(AssetResource\Listing $listing): AssetResource\Listing
    {
        $params = $this->getFilterParams();

        if (empty($params)) {
            return $listing;
        }

        foreach ($params as $filterKey => $filterValue) {

            $className = 'FilterBy'.StrHelper::snakeToPascal($filterKey);

            $listing = $this->process($listing, $className, $filterValue);
        }

        return $listing;
    }

    protected function process(AssetResource\Listing $listing, string $className, mixed $value): AssetResource\Listing
    {
        $classNameSpace = "Froq\PortalBundle\Manager\AssetResource\ModelFilter\Filters\\$className";

        if (!class_exists($classNameSpace)) {
            return $listing;
        }

        /** @var FilterInterface $filter */
        $filter = $this->app->getContainer()->get($classNameSpace);

        if (!$filter instanceof FilterInterface || !$filter->supports($value)) {
            return $listing;
        }

        return $filter->apply($listing, $value);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getFilterParams(): array
    {
        $params = $this->request?->query?->all();

        if (!empty($params['filters'])) {
            $filters = $params['filters'];
        } else {
            $filters = [];
        }

        $excludes = [
            'sorting_direction',
            'sort_order'
        ];

        foreach ($excludes as $exclude) {
            if (isset($filters[$exclude])) {
                unset($filters[$exclude]);
            }
        }

        return $filters;
    }
}
