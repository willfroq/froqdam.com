<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\Processor;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\ValueObject\ProjectFromPayload;
use Pimcore\Db;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Project;

final class CreateProject
{
    public function __construct(

    ) {
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     *
     * @param array<int, string> $actions
     */
    public function __invoke(
        ProjectFromPayload $projectFromPayload,
        AssetResource $parentAssetResource,
        Organization $organization,
        DataObject $parentProjectFolder,
        array &$actions
    ): void {
        $project = new Project();

        $project->setKey((string) $projectFromPayload->projectCode);
        $project->setFroq_name($projectFromPayload->froqName);
        $project->setName($projectFromPayload->projectName);
        $project->setCode($projectFromPayload->projectCode);
        $project->setPim_project_number((string) $projectFromPayload->pimProjectNumber);
        $project->setFroq_project_number($projectFromPayload->froqProjectNumber);
        $project->setCustomer_project_number2($projectFromPayload->customerProjectNumber);
        $project->setDescription($projectFromPayload->description);
        $project->setProject_type($projectFromPayload->projectType);
        $project->setStatus($projectFromPayload->status);
        $project->setLocation($projectFromPayload->location);
        $project->setDeliveryType($projectFromPayload->deliveryType);

        // TODO Contacts, startDate, EndDate, ProjectFields, SuppliedMaterial

        $statement = Db::get()->prepare('SELECT Assets FROM object_Project WHERE o_id = ?;');

        $statement->bindValue(1, $project->getId(), \PDO::PARAM_INT);

        $relatedAssetResourceIds = array_filter(explode(',', (string) $statement->executeQuery()->fetchOne())); /** @phpstan-ignore-line */
        $previouslyRelatedAssetResources = [];

        foreach ($relatedAssetResourceIds as $assetResourceId) {
            $assetResource = AssetResource::getById((int) $assetResourceId);

            if (!($assetResource instanceof AssetResource)) {
                continue;
            }

            if (!$assetResource->hasChildren()) {
                continue;
            }

            if (str_contains(haystack: (string) $assetResource->getName(), needle: (string) $project->getCode())) {
                $previouslyRelatedAssetResources[] = $assetResource;

                continue;
            }

            if (str_contains(haystack: (string) $assetResource->getName(), needle: (string) $project->getFroq_project_number())) {
                $previouslyRelatedAssetResources[] = $assetResource;

                continue;
            }

            if (str_contains(haystack: (string) $assetResource->getName(), needle: (string) $project->getPim_project_number())) {
                $previouslyRelatedAssetResources[] = $assetResource;
            }
        }

        $assetResources = array_values(array_filter(array_unique([...$previouslyRelatedAssetResources, $parentAssetResource])));

        $project->setAssets($assetResources);
        $project->setPublished(true);
        $project->setCustomer($organization);

        $project->setParentId((int) $parentProjectFolder->getId());

        $project->save();

        $actions[] = sprintf(
            'Project with ID %d is created with related AssetResource ids: %s',
            $project->getId(),
            implode(',', array_map(fn (AssetResource $assetResource) => $assetResource->getId(), $assetResources))
        );
    }
}
