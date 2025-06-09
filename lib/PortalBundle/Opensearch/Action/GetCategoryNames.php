<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action;

use Pimcore\Model\DataObject\Category;
use Pimcore\Model\DataObject\ColourGuideline;

final class GetCategoryNames
{
    /**
     * @return array<int, string>
     */
    public function __invoke(ColourGuideline $colourGuideline, string $type): array
    {
        $names = [];

        $categories = match ($type) {
            'markets' => $colourGuideline->getMarkets(),
            'brands' => $colourGuideline->getBrands(),
            'campaigns' => $colourGuideline->getCampaigns(),

            default => []
        };

        foreach ($categories as $category) {
            if (!($category instanceof Category)) {
                continue;
            }

            $names[] = $category->getName();
        }

        return array_values(array_filter(array_unique($names)));
    }
}
