<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Repository;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Pimcore\Db;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Product;

final class ProductRepository
{
    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getRelatedProductId(string $assetResourceId): int
    {
        $sql = 'SELECT object_Product.oo_id FROM object_Product WHERE FIND_IN_SET(?, object_Product.Assets)';

        $statement = Db::get()->prepare($sql);
        $statement->bindValue(1, $assetResourceId, \PDO::PARAM_STR);

        return (int) $statement->executeQuery()->fetchOne(); /** @phpstan-ignore-line */
    }

    /**
     * @throws \Exception
     */
    public function isProductExists(Organization $organization, string $productKey, string $sku, string $ean): bool
    {
        $parentProductFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Products->readable())
            ->addConditionParam('o_path = ?', $organization->getObjectFolder() . '/')
            ->current();

        if (!($parentProductFolder instanceof DataObject)) {
            throw new \Exception(message: 'No Product folder folder i.e. /Customers/org-name/Products/');
        }

        $product = (new Product\Listing())
            ->addConditionParam('o_key = ?', $productKey)
            ->addConditionParam('o_path = ?', $organization->getObjectFolder().'/'.AssetResourceOrganizationFolderNames::Products->readable().'/')
            ->current();

        if ($product instanceof Product) {
            return true;
        }

        return false;
    }
}
