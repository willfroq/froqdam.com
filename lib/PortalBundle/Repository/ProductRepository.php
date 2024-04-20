<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Repository;

use Doctrine\DBAL\Driver\Exception;
use Pimcore\Db;

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
}
