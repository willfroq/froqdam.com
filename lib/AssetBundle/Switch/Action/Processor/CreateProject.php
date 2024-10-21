<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\Processor;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\ValueObject\ProjectFromPayload;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Project;

final class CreateProject
{
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

        $assetResources = array_values(array_filter(array_unique([...$project->getAssets(), $parentAssetResource])));

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
