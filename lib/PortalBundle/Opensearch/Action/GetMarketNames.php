<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action;

use Pimcore\Model\DataObject\ColourGuideline;

final class GetMarketNames
{
    /**
     * @param ColourGuideline $colourGuideline
     *
     * @return array<int, string>
     */
    public function __invoke(ColourGuideline $colourGuideline): array
    {
        $category = $colourGuideline->getCategory();

        if (strtolower((string) $category?->getReportingType()) !== 'market') {
            return [];
        }

        return (array) $category?->getBrands();
    }
}
