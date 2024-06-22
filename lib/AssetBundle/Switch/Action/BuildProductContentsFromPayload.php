<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Switch\Action\Processor\SetProductContents;
use Froq\AssetBundle\Switch\ValueObject\ProductFromPayload;
use Pimcore\Model\DataObject\Product;

final class BuildProductContentsFromPayload
{
    public function __construct(private readonly SetProductContents $setProductContents)
    {
    }

    public function __invoke(Product $product, ProductFromPayload $productFromPayload, bool $isUpdate): void
    {
        if (isset($productFromPayload->productNetContents) && is_array($productFromPayload->productNetContents)) {
            ($this->setProductContents)($product, $productFromPayload->productNetContents, true, $isUpdate);
        }

        if (isset($productFromPayload->productNetUnitContents) && is_array($productFromPayload->productNetUnitContents)) {
            ($this->setProductContents)($product, $productFromPayload->productNetUnitContents, false, $isUpdate);
        }
    }
}
