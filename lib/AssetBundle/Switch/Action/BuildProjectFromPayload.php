<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\ValueObject\ProjectFromPayload;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Froq\PortalBundle\Repository\ProjectRepository;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Project;

final class BuildProjectFromPayload
{
    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull,
    ) {
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     *
     * @param array<int, string> $actions
     * @param array<int, AssetResource> $assetResources
     */
    public function __invoke(
        SwitchUploadRequest $switchUploadRequest,
        array $assetResources,
        Organization $organization,
        array $actions
    ): void {
        $rootProjectFolder = $organization->getObjectFolder() . '/';

        $parentProjectFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Projects->name)
            ->addConditionParam('o_path = ?', $rootProjectFolder)
            ->current();

        if (!($parentProjectFolder instanceof DataObject)) {
            return;
        }

        $projectData = (array) json_decode($switchUploadRequest->projectData, true);

        if (($this->allPropsEmptyOrNull)($projectData)) {
            return;
        }

        $projectFromPayload = new ProjectFromPayload(
            projectCode: $projectData['projectCode'] ?? null,
            projectName: $projectData['projectName'] ?? null,
            pimProjectNumber: $projectData['pimProjectNumber'] ?? null,
            froqProjectNumber: $projectData['froqProjectNumber'] ?? null,
            customerProjectNumber: $projectData['customerProjectNumber'] ?? null,
            froqName: $projectData['froqName'] ?? null,
            description: $projectData['description'] ?? null,
            projectType: $projectData['projectType'] ?? null,
            status: $projectData['status'] ?? null,
            location: $projectData['location'] ?? null,
            deliveryType: $projectData['deliveryType'] ?? null,
        );

        $projectCode = $projectFromPayload->projectCode;

        if ($this->projectRepository->isProjectExists($organization, (string) $projectCode)) {
            return;
        }

        $assetResourceId = current($assetResources) instanceof AssetResource ? current($assetResources)->getId() : '';

        $project = Project::getById($this->projectRepository->getRelatedProjectId((string) $assetResourceId));

        if (!($project instanceof Project)) {
            $project = new Project();
        }

        $project->setCode($projectCode);
        $project->setKey((string) $projectCode);

        $project->setName($projectFromPayload->projectName);
        $project->setPim_project_number($projectFromPayload->pimProjectNumber);
        $project->setFroq_project_number($projectFromPayload->froqProjectNumber);
        $project->setCustomer_project_number2($projectFromPayload->customerProjectNumber);
        $project->setFroq_name($projectFromPayload->froqName);
        $project->setDescription($projectFromPayload->description);
        $project->setProject_type($projectFromPayload->projectType);
        $project->setStatus($projectFromPayload->status);
        $project->setLocation($projectFromPayload->location);
        $project->setDeliveryType($projectFromPayload->deliveryType);

        // TODO Contacts, startDate, EndDate, ProjectFields, SuppliedMaterial

        $existingAssetResources = [...$project->getAssets(), ...$assetResources];

        $assetResources = array_unique($existingAssetResources);

        $project->setAssets($assetResources);
        $project->setPublished(true);
        $project->setCustomer($organization);

        $project->setParentId((int) $parentProjectFolder->getId());

        $project->save();
    }
}
