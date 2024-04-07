<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Repository;

use Doctrine\DBAL\ForwardCompatibility\Result;
use Doctrine\DBAL\Query\QueryBuilder;
use Pimcore\Db;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\AssetType;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\User;

class AssetTypeRepository
{
    /**
     * @return array<int, AssetType|null>|null
     */
    public function findAssetTypesForUser(User $user): ?array
    {
        $list = new AssetType\Listing();

        $list->onCreateQueryBuilder(function (QueryBuilder $queryBuilder) use ($user) {
            $assetRscTable = 'object_' . AssetResource::classId();
            $assetRscAlias = 'oar';

            $assetTypeClassId = AssetType::classId();
            $assetTypeTable = 'object_' . $assetTypeClassId;
            $assetTypeAlias = 'oat';

            $organizationRelationsTable = 'object_relations_' . Organization::classId();
            $organizationAlias = 'oro';

            $userRelationsTable = 'object_relations_' . User::classId();
            $userAlias = 'oru';

            $expr = $queryBuilder->expr();
            $qb = Db::get()->createQueryBuilder();
            $qb
                ->select('DISTINCT(' . $assetRscAlias . '.' . $assetTypeClassId . '__id)')
                ->from($assetRscTable, $assetRscAlias)
                ->innerJoin($assetRscAlias, $assetTypeTable, $assetTypeAlias, $expr->eq($assetTypeAlias . '.o_id', $assetRscAlias . '.' . $assetTypeClassId . '__id'))
                ->innerJoin($assetRscAlias, $organizationRelationsTable, $organizationAlias, $expr->eq($assetRscAlias . '.oo_id', $organizationAlias . '.dest_id'))
                ->innerJoin($organizationAlias, $userRelationsTable, $userAlias, $expr->eq($organizationAlias . '.src_id', $userAlias . '.dest_id'))
                ->where(
                    $expr->and(
                        $expr->eq($organizationAlias . '.fieldname', $expr->literal('AssetResources')),
                        $expr->eq($userAlias . '.fieldname', $expr->literal('Organizations')),
                        $expr->eq($userAlias . '.src_id', ':userId')
                    )
                )
                ->setParameters(['userId' => $user->getId()]);

            $result = $qb->execute();

            if ($result instanceof Result) {
                $assetTypeIds = array_column($result->fetchAllAssociative(), $assetTypeClassId . '__id');
            }

            if (!empty($assetTypeIds)) {
                $queryBuilder->where($expr->in($assetTypeTable . '.o_id', $assetTypeIds));
            } else {
                $queryBuilder->where($expr->eq($assetTypeTable . '.o_id', 0));
            }

            $queryBuilder->orderBy($assetTypeTable . '.name', 'asc');

            return $queryBuilder;
        });

        return $list->getObjects();
    }
}
