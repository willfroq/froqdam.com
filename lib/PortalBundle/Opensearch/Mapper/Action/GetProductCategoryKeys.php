<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Mapper\Action;

use Froq\AssetBundle\Switch\Enum\CategoryNames;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Category;
use Pimcore\Model\DataObject\Product;

final class GetProductCategoryKeys
{
    /** @return array<int, string> */
    public function __invoke(AssetResource $parentAssetResource, string $levelLabel): array
    {
        if (!in_array(needle: $levelLabel, haystack: array_column(array: CategoryNames::cases(), column_key: 'name'))) {
            return [];
        }

        $categoryKeys = [];

        foreach ($parentAssetResource->getProducts() as $product) {
            if (!($product instanceof Product)) {
                continue;
            }

            foreach ($product->getCategories() as $category) {
                if (!($category instanceof Category)) {
                    continue;
                }

                if ($category->getLevelLabel() !== $levelLabel) {
                    continue;
                }

                $categoryKeys[] = $category->getKey();
            }
        }

        return array_values(array_unique(array_filter($categoryKeys, fn (?string $categoryKey) => $categoryKey !== null)));
    }
}
