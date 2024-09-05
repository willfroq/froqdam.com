<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Exception;
use Froq\AssetBundle\Switch\Action\RelatedObject\CreateProjectFolder;
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
        private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull,
        private readonly CreateProjectFolder $createProjectFolder,
        private readonly ProjectRepository $projectRepository,
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
        array $actions,
        bool $isUpdate = false
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

        if (empty($projectData) || ($this->allPropsEmptyOrNull)($projectData)) {
            return;
        }

        $projectPath = $rootProjectFolder.AssetResourceOrganizationFolderNames::Projects->readable().'/';

        $existingProjects = (new Project\Listing())
            ->addConditionParam('o_path = ?', $projectPath)
            ->load();

        $project = null;

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

        if ($this->projectRepository->isPayloadProjectCodeExistsInExistingProjects(
            $existingProjects,
            (string)$projectFromPayload->projectCode,
            (string)$projectFromPayload->froqProjectNumber
        )) {
            $project = $this->projectRepository->getProjectFromExistingProjects(
                $existingProjects,
                (string) $projectFromPayload->projectCode,
                (string) $projectFromPayload->froqProjectNumber
            );

            if ($project instanceof Project) {
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
            }
        }

        if (!$this->projectRepository->isPayloadProjectCodeExistsInExistingProjects(
            $existingProjects,
            (string)$projectFromPayload->projectCode,
            (string)$projectFromPayload->froqProjectNumber
        )) {
            $project = new Project();

            $project->setKey((string) $projectFromPayload->froqName);
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
        }

        if (!($project instanceof Project)) {
            return;
        }

        // TODO Contacts, startDate, EndDate, ProjectFields, SuppliedMaterial

        $assetResources = array_values(array_filter(array_unique([...$project->getAssets(), ...$assetResources])));

        $project->setAssets($assetResources);
        $project->setPublished(true);
        $project->setCustomer($organization);

        $project->setParentId((int) $parentProjectFolder->getId());

        $project->save();
    }
}
