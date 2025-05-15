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
    public function __invoke(ColourGuideline $colourGuideline, string $reportingType): array
    {
        $names = [];

        $categories = $colourGuideline->getCategories();

        foreach ($categories as $category) {
            if (!($category instanceof Category)) {
                continue;
            }

            if (strtolower((string) $category->getReportingType()) !== $reportingType) {
                continue;
            }

            $names[] = $category->getName();
        }

        return array_values(array_filter(array_unique($names)));
    }
}
