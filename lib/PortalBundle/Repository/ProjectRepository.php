<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Repository;

use Doctrine\DBAL\Driver\Exception;
use Pimcore\Db;

final class ProjectRepository
{
    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getRelatedProjectId(string $assetResourceId): int
    {
        $sql = 'SELECT object_Project.oo_id FROM object_Project WHERE FIND_IN_SET(?, object_Project.Assets)';

        $statement = Db::get()->prepare($sql);
        $statement->bindValue(1, $assetResourceId, \PDO::PARAM_STR);

        return (int) $statement->executeQuery()->fetchOne(); /** @phpstan-ignore-line */
    }
}
