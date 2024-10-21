<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Doctrine\DBAL\Exception;
use Froq\AssetBundle\Switch\Action\Processor\CreateProject;
use Froq\AssetBundle\Switch\Action\Processor\UpdateProject;
use Froq\AssetBundle\Switch\Action\RelatedObject\CreateProjectFolder;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
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
        private readonly CreateProject $createProject,
        private readonly UpdateProject $updateProject,
    ) {
    }

    /**
     * @param array<int, string> $actions
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws Exception
     * @throws \Exception
     */
    public function __invoke(
        SwitchUploadRequest $switchUploadRequest,
        AssetResource $parentAssetResource,
        Organization $organization,
        array &$actions,
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
            ($this->updateProject)(
                $project,
                $projectFromPayload,
                $parentAssetResource,
                $organization,
                $parentProjectFolder,
                $actions
            );

            return;
        }

        ($this->createProject)(
            $projectFromPayload,
            $parentAssetResource,
            $organization,
            $parentProjectFolder,
            $actions
        );
    }
}
