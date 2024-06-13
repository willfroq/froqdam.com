<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\Processor;

use Froq\AssetBundle\Switch\ValueObject\ProductFromPayload;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;

final class ProcessProduct
{
    /**
     * @throws \Exception
     */
    public function __invoke(Organization $organization, ProductFromPayload $productFromPayload): Product
    {
        $productFromEan = Product::getByEAN((string) $productFromPayload->productEAN)?->current(); /** @phpstan-ignore-line */
        $productFromSku = Product::getBySKU((string) $productFromPayload->productSKU)?->current(); /** @phpstan-ignore-line */
        if ($productFromEan instanceof Product) {
            return $productFromEan;
        }

        if ($productFromSku instanceof Product) {
            return $productFromSku;
        }

        return new Product();
    }
}
