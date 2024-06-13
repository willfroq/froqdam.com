<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Repository;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Pimcore\Db;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Project;

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

    /**
     * @throws \Exception
     */
    public function isProjectExists(Organization $organization, string $code): bool
    {
        $parentProjectFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Projects->readable())
            ->addConditionParam('o_path = ?', $organization->getObjectFolder() . '/')
            ->current();

        if (!($parentProjectFolder instanceof DataObject)) {
            throw new \Exception(message: 'No Project folder folder i.e. /Customers/org-name/Projects/');
        }

        $project = (new Project\Listing())
            ->addConditionParam('o_key = ?', $code)
            ->addConditionParam('o_path = ?', $organization->getObjectFolder().'/'.AssetResourceOrganizationFolderNames::Projects->readable().'/')
            ->current();

        if ($project instanceof Project) {
            return true;
        }

        return false;
    }
}
