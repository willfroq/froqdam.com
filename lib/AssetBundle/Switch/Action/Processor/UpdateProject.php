<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\Processor;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\ValueObject\ProjectFromPayload;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Project;

final class UpdateProject
{
    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     *
     * @param array<int, string> $actions
     */
    public function __invoke(
        Project $project,
        ProjectFromPayload $projectFromPayload,
        AssetResource $parentAssetResource,
        Organization $organization,
        DataObject $parentProjectFolder,
        array &$actions
    ): void {
        if (empty($project->getFroq_name())) {
            $project->setKey((string) $projectFromPayload->froqName);
            $project->setFroq_name($projectFromPayload->froqName);
        }

        if (empty($project->getName())) {
            $project->setName($projectFromPayload->projectName);
        }

        if (empty($project->getCode())) {
            $project->setCode($projectFromPayload->projectCode);
        }

        if (empty($project->getPim_project_number())) {
            $project->setPim_project_number((string) $projectFromPayload->pimProjectNumber);
        }

        if (empty($project->getFroq_project_number())) {
            $project->setFroq_project_number($projectFromPayload->froqProjectNumber);
        }

        if (empty($project->getCustomer_project_number2())) {
            $project->setCustomer_project_number2($projectFromPayload->customerProjectNumber);
        }

        if (empty($project->getDescription())) {
            $project->setDescription($projectFromPayload->description);
        }

        if (empty($project->getProject_type())) {
            $project->setProject_type($projectFromPayload->projectType);
        }

        if (empty($project->getStatus())) {
            $project->setStatus($projectFromPayload->status);
        }

        if (empty($project->getLocation())) {
            $project->setLocation($projectFromPayload->location);
        }

        if (empty($project->getDeliveryType())) {
            $project->setDeliveryType($projectFromPayload->deliveryType);
        }

        // TODO Contacts, startDate, EndDate, ProjectFields, SuppliedMaterial

        $assetResources = array_values(array_filter(array_unique([...$project->getAssets(), $parentAssetResource])));

        $project->setAssets($assetResources);
        $project->setPublished(true);
        $project->setCustomer($organization);

        $project->setParentId((int) $parentProjectFolder->getId());

        $project->save();

        $actions[] = sprintf(
            'Project with ID %d is updated with related AssetResource ids: %s',
            $project->getId(),
            implode(',', array_map(fn (AssetResource $assetResource) => $assetResource->getId(), $assetResources))
        );
    }
}
