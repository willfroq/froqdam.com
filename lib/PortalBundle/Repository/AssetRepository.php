<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Repository;

use Doctrine\DBAL\Driver\Exception;
use Pimcore\Db;
use Pimcore\Model\Asset;

final class AssetRepository
{
    /**
     * @return array<int, Asset>
     *
     * @throws Exception|\Doctrine\DBAL\Exception
     */
    public function getDeepestChildren(): array
    {
        $asset = new Asset\Listing();

        $deepestChildrenIds = [];
        foreach ($this->fetchParentIds() as $parentId) {
            if (!$this->hasChildren($parentId)) {
                $deepestChildrenIds[] = $parentId;
            }

            $deepestChildrenIds[] = $this->fetchDeepestChildId($parentId);
        }

        $asset->setCondition('assets.id IN (:deepestChildrenIds)', [
            'deepestChildrenIds' => $deepestChildrenIds
        ]);

        return $asset->getItems(1, 1000);
    }

    /**
     * @return array<int, int>
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
     */
    private function fetchParentIds(): array
    {
        $sql = "SELECT id FROM assets WHERE SUBSTRING_INDEX(path, '/', -2) = 'Assets/'";

        return Db::get()->prepare($sql)->executeQuery()->fetchFirstColumn(); /** @phpstan-ignore-line */
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchDeepestChildId(int $parentId): int
    {
        $sql = 'SELECT MAX(id) FROM assets WHERE parentId = ?';

        $statement = Db::get()->prepare($sql);
        $statement->bindValue(1, $parentId, \PDO::PARAM_INT);

        return (int) $statement->executeQuery()->fetchOne(); /** @phpstan-ignore-line */
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    private function hasChildren(int $id): bool
    {
        $sql = 'SELECT COUNT(*) FROM assets WHERE ? = parentId;';

        $statement = Db::get()->prepare($sql);
        $statement->bindValue(1, $id, \PDO::PARAM_INT);

        $result = $statement->executeQuery(); /** @phpstan-ignore-line */
        if ($result->fetchOne() > 0) {
            return true;
        }

        return false;
    }
}
