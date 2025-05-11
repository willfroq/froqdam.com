<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action\Update;

use Froq\AssetBundle\Switch\Action\RelatedObject\CreateProjectFolder;
use Froq\AssetBundle\Switch\Controller\Request\UpdateRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\ValueObject\ProjectFromPayload;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Project;

final class BuildProjectFromPayload
{
    public function __construct(
        private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull,
        private readonly CreateProjectFolder $createProjectFolder,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(
        UpdateRequest $updateRequest,
        AssetResource $parentAssetResource,
    ): void {
        $rootProjectFolder = $updateRequest->parentAssetResourceFolderPath;

        $organization = Organization::getById($updateRequest->organizationId);

        if (!($organization instanceof Organization)) {
            throw new \Exception(message: 'Organization does not exists.');
        }

        $parentProjectFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Projects->readable())
            ->addConditionParam('o_path = ?', $rootProjectFolder)
            ->current();

        if (!($parentProjectFolder instanceof DataObject)) {
            $parentProjectFolder = ($this->createProjectFolder)($organization, $rootProjectFolder);
        }

        $projectData = (array) json_decode($updateRequest->projectData, true);

        if (empty($projectData) || ($this->allPropsEmptyOrNull)($projectData)) {
            return;
        }

        $projectPath = $rootProjectFolder.AssetResourceOrganizationFolderNames::Projects->readable().'/';

        $projectFromPayload = new ProjectFromPayload(
            projectCode: $projectData['projectCode'] ?? '',
            projectName: $projectData['projectName'] ?? '',
            pimProjectNumber: $projectData['pimProjectNumber'] ?? '',
            froqProjectNumber: $projectData['froqProjectNumber'] ?? '',
            customerProjectNumber: $projectData['customerProjectNumber'] ?? '',
            froqName: $projectData['froqName'] ?? '',
            description: $projectData['description'] ?? '',
            projectType: $projectData['projectType'] ?? '',
            status: $projectData['status'] ?? '',
            location: $projectData['location'] ?? '',
            deliveryType: $projectData['deliveryType'] ?? '',
        );

        $project = null;

        if (!empty($projectFromPayload->froqProjectNumber)) {
            $project = (new Project\Listing())
                ->addConditionParam('froq_project_number = ?', $projectFromPayload->froqProjectNumber)
                ->addConditionParam('o_path = ?', $projectPath)
                ->current();
        }

        if ($project instanceof Project) {
            $this->updateProject($project, $projectFromPayload, $organization, $parentAssetResource, $parentProjectFolder);

            return;
        }

        if (empty($projectFromPayload->froqProjectNumber)) {
            return;
        }

        $this->createProject($projectFromPayload, $organization, $parentAssetResource, $parentProjectFolder);
    }

    /**
     * @throws \Exception
     */
    private function updateProject(Project $project, ProjectFromPayload $projectFromPayload, Organization $organization, AssetResource $parentAssetResource, DataObject $parentProjectFolder): void
    {
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

        $assetResources = array_values(array_filter(array_unique([...$project->getAssets(), $parentAssetResource])));

        $project->setAssets($assetResources);
        $project->setPublished(true);
        $project->setCustomer($organization);

        $project->setParentId((int) $parentProjectFolder->getId());

        $project->save();
    }

    /**
     * @throws \Exception
     */
    private function createProject(ProjectFromPayload $projectFromPayload, Organization $organization, AssetResource $parentAssetResource, DataObject $parentProjectFolder): void
    {
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

        $assetResources = array_values(array_filter(array_unique([...$project->getAssets(), $parentAssetResource])));

        $project->setAssets($assetResources);
        $project->setPublished(true);
        $project->setCustomer($organization);

        $project->setParentId((int) $parentProjectFolder->getId());

        $project->save();
    }
}
