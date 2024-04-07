<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Action;

use Froq\PortalBundle\Enum\ProductContents;
use Symfony\Component\HttpFoundation\Request;

final class ProcessProductContentsRequestFilter
{
    public function __construct(
        private readonly BuildNetContentsRequestFilter $buildNetContentsRequestFilter,
        private readonly BuildNetUnitContentsRequestFilter $buildNetUnitContentsRequestFilter
    ) {
    }

    public function __invoke(Request $request): Request
    {
        /** @var array<int, array<string, array<string, string>>> $requestFilters */
        $requestFilters = (array) $request->query->get(key: 'filters', default: '');

        if ($isNetContents = key((array) current($requestFilters)) === ProductContents::NetContents->readable()) {
            /** @var array<int, array<string, array<string, string>>> $requestFilters */
            $requestFilters = ($this->buildNetContentsRequestFilter)(
                requestFilters: $requestFilters,
                filterName: ProductContents::NetContents->readable(),
                isNetContents: $isNetContents
            );

            $request->query->set('filters', $requestFilters);
        }

        if ($isNetContents = key((array) current($requestFilters)) === ProductContents::NetContentsMillilitre->readable()) {
            /** @var array<int, array<string, array<string, string>>> $requestFilters */
            $requestFilters = ($this->buildNetContentsRequestFilter)(
                requestFilters: $requestFilters,
                filterName: ProductContents::NetContentsMillilitre->readable(),
                isNetContents: $isNetContents
            );

            $request->query->set('filters', $requestFilters);
        }

        if ($isNetContents = key((array) current($requestFilters)) === ProductContents::NetContentsGrams->readable()) {
            /** @var array<int, array<string, array<string, string>>> $requestFilters */
            $requestFilters = ($this->buildNetContentsRequestFilter)(
                requestFilters: $requestFilters,
                filterName: ProductContents::NetContentsGrams->readable(),
                isNetContents: $isNetContents
            );

            $request->query->set('filters', $requestFilters);
        }

        if (key((array) current($requestFilters)) === ProductContents::NetContentsPieces->readable() || key((array) current($requestFilters)) === ProductContents::NetContentsEach->readable()) {
            /** @var array<int, array<string, array<string, string>>> $requestFilters */
            $requestFilters = ($this->buildNetContentsRequestFilter)(
                requestFilters: $requestFilters,
                filterName: ProductContents::NetContentsPieces->readable(),
                isNetContents: true
            );

            $request->query->set('filters', $requestFilters);
        }

        if ($isNetUnitContents = key((array) current($requestFilters)) === ProductContents::NetUnitContents->readable()) {
            /** @var array<int, array<string, array<string, string>>> $requestFilters */
            $requestFilters = ($this->buildNetUnitContentsRequestFilter)(
                requestFilters: $requestFilters,
                filterName: ProductContents::NetUnitContents->readable(),
                isNetUnitContents: $isNetUnitContents
            );

            $request->query->set('filters', $requestFilters);
        }

        if ($isNetUnitContents = key((array) current($requestFilters)) === ProductContents::NetUnitContentsMillilitre->readable()) {
            /** @var array<int, array<string, array<string, string>>> $requestFilters */
            $requestFilters = ($this->buildNetUnitContentsRequestFilter)(
                requestFilters: $requestFilters,
                filterName: ProductContents::NetUnitContentsMillilitre->readable(),
                isNetUnitContents: $isNetUnitContents
            );

            $request->query->set('filters', $requestFilters);
        }

        if ($isNetUnitContents = key((array) current($requestFilters)) === ProductContents::NetUnitContentsGrams->readable()) {
            /** @var array<int, array<string, array<string, string>>> $requestFilters */
            $requestFilters = ($this->buildNetUnitContentsRequestFilter)(
                requestFilters: $requestFilters,
                filterName: ProductContents::NetUnitContentsGrams->readable(),
                isNetUnitContents: $isNetUnitContents
            );

            $request->query->set('filters', $requestFilters);
        }

        return $request;
    }
}
