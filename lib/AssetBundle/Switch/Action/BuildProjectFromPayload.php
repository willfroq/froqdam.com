<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Froq\AssetBundle\Utility\IsPathExists;
use Froq\PortalBundle\Repository\ProjectRepository;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Project;

final class BuildProjectFromPayload
{
    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull,
        private readonly IsPathExists $isPathExists,
        private readonly ApplicationLogger $logger,
    ) {
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     *
     * @param array<int, string> $actions
     */
    public function __invoke(SwitchUploadRequest $switchUploadRequest, AssetResource $assetResource, Organization $organization, array $actions): void
    {
        $rootProjectFolder = $organization->getObjectFolder() . '/';

        $parentProjectFolder = (new DataObject\Listing())
            ->addConditionParam('o_key = ?', AssetResourceOrganizationFolderNames::Projects->name)
            ->addConditionParam('o_path = ?', $rootProjectFolder)
            ->current();

        if (!($parentProjectFolder instanceof DataObject)) {
            return;
        }

        $payload = (array) json_decode($switchUploadRequest->projectData, true);

        if (!isset($payload['projectCode'])) {
            return;
        }

        if (($this->allPropsEmptyOrNull)($payload)) {
            return;
        }

        $projectCode = $payload['projectCode'];

        if ($this->projectRepository->isProjectExists($organization, (string) $projectCode)) {
            return;
        }

        $assetResourceId = (string) $assetResource->getId();

        $project = Project::getById($this->projectRepository->getRelatedProjectId($assetResourceId));

        if (!($project instanceof Project)) {
            $project = new Project();
        }

        $project->setCode($projectCode);
        $project->setKey($projectCode);

        if (isset($payload['projectName'])) {
            $project->setName($payload['projectName']);
        }
        if (isset($payload['pimProjectNumber'])) {
            $project->setPim_project_number($payload['pimProjectNumber']);
        }
        if (isset($payload['froqProjectNumber'])) {
            $project->setFroq_project_number($payload['froqProjectNumber']);
        }
        if (isset($payload['customerProjectNumber'])) {
            $project->setCustomer_project_number2($payload['customerProjectNumber']);
        }
        if (isset($payload['froqName'])) {
            $project->setFroq_name($payload['froqName']);
        }
        if (isset($payload['description'])) {
            $project->setDescription($payload['description']);
        }
        if (isset($payload['projectType'])) {
            $project->setProject_type($payload['projectType']);
        }
        if (isset($payload['status'])) {
            $project->setStatus($payload['status']);
        }
        if (isset($payload['location'])) {
            $project->setStatus($payload['location']);
        }
        if (isset($payload['deliveryType'])) {
            $project->setStatus($payload['deliveryType']);
        }
        if (isset($payload['deliveryFormat'])) {
            $project->setStatus($payload['deliveryFormat']);
        }
        // TODO Contacts, startDate, EndDate, ProjectFields, SuppliedMaterial

        $assetResources = array_unique([...$project->getAssets(), $assetResource]);

        $projectPath = $rootProjectFolder.AssetResourceOrganizationFolderNames::Projects->name;

        if (($this->isPathExists)($switchUploadRequest, $projectPath)) {
            $message = sprintf('Related project NOT created. %s path already exists, this has to be unique.', $projectPath);

            $actions[] = $message;

            $this->logger->error(message: $message . implode(separator: ',', array: $actions), context: [
                'component' => $switchUploadRequest->eventName
            ]);
        }

        if (!($this->isPathExists)($switchUploadRequest, $projectPath)) {
            $project->setAssets($assetResources);
            $project->setPublished(true);
            $project->setCustomer($organization);

            $project->setParentId((int) $parentProjectFolder->getId());

            $project->save();
        }
    }
}
