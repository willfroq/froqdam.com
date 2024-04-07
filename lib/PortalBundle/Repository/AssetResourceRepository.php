<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Repository;

use Doctrine\DBAL\Driver\Exception;
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

        if(!$this->authorizationChecker->isGranted('view', $assetResource)) {
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
}
