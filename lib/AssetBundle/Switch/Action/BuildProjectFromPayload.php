<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Exception;
use Froq\AssetBundle\Switch\Action\RelatedObject\CreateProjectFolder;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Switch\ValueObject\ProjectFromPayload;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Froq\AssetBundle\Utility\IsPathExists;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Project;

final class BuildProjectFromPayload
{
    public function __construct(
        private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull,
        private readonly IsPathExists $isPathExists,
        private readonly CreateProjectFolder $createProjectFolder,
    ) {
    }

    /**
     * @throws Exception
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
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Projects->readable())
            ->addConditionParam('o_path = ?', $rootProjectFolder)
            ->current();

        if (!($parentProjectFolder instanceof DataObject)) {
            $parentProjectFolder = ($this->createProjectFolder)($organization, $rootProjectFolder);
        }

        $projectData = (array) json_decode($switchUploadRequest->projectData, true);

        if (!isset($projectData['projectCode'])) {
            return;
        }

        if (!isset($projectData['froqName'])) {
            return;
        }

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

        $project = Project::getByFroq_project_number($projectFromPayload->froqProjectNumber)?->current(); /** @phpstan-ignore-line */
        if (!($project instanceof Project)) {
            $project = new Project();
        }

        $projectKey = $projectFromPayload->froqName;

        if ($project->getFroq_name() === null) {
            $project->setKey((string) $projectKey);
            $project->setFroq_name($projectFromPayload->froqName);
        }

        if ($project->getName() === null) {
            $project->setName($projectFromPayload->projectName);
        }

        if ($project->getCode() === null) {
            $project->setCode($projectCode);
        }

        if ($project->getPim_project_number() === null) {
            $project->setPim_project_number((string) $projectFromPayload->pimProjectNumber);
        }

        if ($project->getFroq_project_number() === null) {
            $project->setFroq_project_number($projectFromPayload->froqProjectNumber);
        }

        if ($project->getCustomer_project_number2() === null) {
            $project->setCustomer_project_number2($projectFromPayload->customerProjectNumber);
        }

        if ($project->getDescription() === null) {
            $project->setDescription($projectFromPayload->description);
        }

        if ($project->getProject_type() === null) {
            $project->setProject_type($projectFromPayload->projectType);
        }

        if ($project->getStatus() === null) {
            $project->setStatus($projectFromPayload->status);
        }

        if ($project->getLocation() === null) {
            $project->setLocation($projectFromPayload->location);
        }

        if ($project->getDeliveryType() === null) {
            $project->setDeliveryType($projectFromPayload->deliveryType);
        }

        // TODO Contacts, startDate, EndDate, ProjectFields, SuppliedMaterial

        $assetResources = array_values(array_unique([...$project->getAssets(), ...$assetResources]));

        $projectPath = $rootProjectFolder.AssetResourceOrganizationFolderNames::Projects->readable().'/';

        if (!($this->isPathExists)((string) $projectKey, $projectPath)) {
            $project->setAssets($assetResources);
            $project->setPublished(true);
            $project->setCustomer($organization);

            $project->setParentId((int) $parentProjectFolder->getId());

            $project->save();
        }
    }
}
