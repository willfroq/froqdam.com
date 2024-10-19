<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Repository;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\PortalBundle\Contract\AssetResourceRepositoryInterface;
use Pimcore\Db;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AssetResourceRepository implements AssetResourceRepositoryInterface
{
    public function __construct(private readonly AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public function getAssetResourceByIdWithChecks(int $id): ?AssetResource
    {
        $assetResource = AssetResource::getById($id);

        if (!$assetResource) {
            throw new NotFoundHttpException();
        }

        if (!$this->authorizationChecker->isGranted('view', $assetResource)) {
            $exception = new AccessDeniedException('Access Denied.');
            $exception->setAttributes(['view']);
            $exception->setSubject($assetResource);

            throw new $exception;
        }

        return $assetResource;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
     */
    public function fetchDeepestChildId(int $parentId): int
    {
        $sql = 'SELECT MAX(oo_id) FROM object_AssetResource WHERE o_parentId = ?';

        $statement = Db::get()->prepare($sql);
        $statement->bindValue(1, $parentId, \PDO::PARAM_INT);

        return (int) $statement->executeQuery()->fetchOne(); /** @phpstan-ignore-line */
    }

    /**
     * @return array<int, int>
     *
     * @throws \Doctrine\DBAL\Exception|Exception
     */
    public function fetchParentIds(int $lastId, int $limit): array
    {
        $sql = "SELECT o_id FROM object_AssetResource
            WHERE (
                SUBSTRING_INDEX(o_path, '/', -2) = ?
                OR SUBSTRING_INDEX(o_path, '/', -2) = ?
                OR SUBSTRING_INDEX(o_path, '/', -2) = ?
                OR SUBSTRING_INDEX(o_path, '/', -2) = ?
                OR SUBSTRING_INDEX(o_path, '/', -2) = ?
            )
            AND o_published = true
            AND o_id > ?
            ORDER BY o_id
            LIMIT ?;"
        ;

        $statement = Db::get()->prepare($sql);

        $statement->bindValue(1, AssetResourceOrganizationFolderNames::Assets->readable().'/', \PDO::PARAM_STR);
        $statement->bindValue(2, AssetResourceOrganizationFolderNames::ThreeDModelLibrary->readable().'/', \PDO::PARAM_STR);
        $statement->bindValue(3, AssetResourceOrganizationFolderNames::Cutter_Guides->readable().'/', \PDO::PARAM_STR);
        $statement->bindValue(4, AssetResourceOrganizationFolderNames::Mockups->readable().'/', \PDO::PARAM_STR);
        $statement->bindValue(5, AssetResourceOrganizationFolderNames::Packshots->readable().'/', \PDO::PARAM_STR);
        $statement->bindValue(6, $lastId, \PDO::PARAM_INT);
        $statement->bindValue(7, $limit, \PDO::PARAM_INT);

        return $statement->executeQuery()->fetchFirstColumn();  /** @phpstan-ignore-line */
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
     */
    public function countParentIds(): int
    {
        $sql = "SELECT COUNT(oo_id) FROM object_AssetResource
            WHERE SUBSTRING_INDEX(o_path, '/', -2) = ?
                OR SUBSTRING_INDEX(o_path, '/', -2) = ?
                OR SUBSTRING_INDEX(o_path, '/', -2) = ?
                OR SUBSTRING_INDEX(o_path, '/', -2) = ?
                OR SUBSTRING_INDEX(o_path, '/', -2) = ?
            AND o_published = true";

        $statement = Db::get()->prepare($sql);

        $statement->bindValue(1, AssetResourceOrganizationFolderNames::Assets->readable().'/', \PDO::PARAM_STR);
        $statement->bindValue(2, AssetResourceOrganizationFolderNames::ThreeDModelLibrary->readable().'/', \PDO::PARAM_STR);
        $statement->bindValue(3, AssetResourceOrganizationFolderNames::Cutter_Guides->readable().'/', \PDO::PARAM_STR);
        $statement->bindValue(4, AssetResourceOrganizationFolderNames::Mockups->readable().'/', \PDO::PARAM_STR);
        $statement->bindValue(5, AssetResourceOrganizationFolderNames::Packshots->readable().'/', \PDO::PARAM_STR);

        return (int) $statement->executeQuery()->fetchOne();  /** @phpstan-ignore-line */
    }
}
