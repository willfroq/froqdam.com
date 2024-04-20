<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Froq\AssetBundle\Utility\AreAllPropsEmptyOrNull;
use Froq\PortalBundle\Repository\ProjectRepository;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Project;

final class BuildProjectFromPayload
{
    public function __construct(private readonly ProjectRepository $projectRepository, private readonly AreAllPropsEmptyOrNull $allPropsEmptyOrNull)
    {
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function __invoke(SwitchUploadRequest $switchUploadRequest, AssetResource $assetResource, Organization $organization): void
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

        if (empty($payload) || ($this->allPropsEmptyOrNull)($payload)) {
            return;
        }

        $assetResourceId = (string) $assetResource->getId();

        $project = Project::getById($this->projectRepository->getRelatedProjectId($assetResourceId));

        if ($organization->getId() !== $project?->getCustomer()?->getId()) {
            return;
        }

        if (!($project instanceof Project)) {
            $project = new Project();
        }

        if (isset($payload['projectCode'])) {
            $project->setCode($payload['projectCode']);
        }
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
            $project->setCustomer_project_number2($payload['froqName']);
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

        $assetResources = [...$project->getAssets(), $assetResource];

        $project->setAssets($assetResources);

        $project->setParentId((int) $parentProjectFolder->getId());

        $project->save();
    }
}
