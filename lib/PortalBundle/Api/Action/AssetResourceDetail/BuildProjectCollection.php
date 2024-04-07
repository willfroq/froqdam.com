<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action\AssetResourceDetail;

use Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection\SettingsSection\BuildProjectInfoSectionCollection;
use Froq\PortalBundle\Api\Action\GetBaseUrl;
use Froq\PortalBundle\Api\Enum\ProjectInfoSectionItems;
use Froq\PortalBundle\Api\Enum\SectionFieldTitles;
use Froq\PortalBundle\Api\Enum\StructuredTableNames;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\ProjectCollection;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\ProjectItem;
use Froq\PortalBundle\Manager\UserSettings\AssetDetail\AssetDetailSettingsManager;
use Froq\PortalBundle\Twig\AssetLibraryExtension;
use Froq\PortalBundle\Twig\PortalDetailExtension;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\Project;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class BuildProjectCollection
{
    public function __construct(
        private readonly PortalDetailExtension $portalDetailExtension,
        private readonly BuildProjectInfoSectionCollection $buildProjectInfoSectionCollection,
        private readonly AssetDetailSettingsManager $assetDetailSettingsManager,
        private readonly AssetLibraryExtension $assetLibraryExtension,
        private readonly GetBaseUrl $getBaseUrl,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function __invoke(AssetResource $assetResource, GroupAssetLibrarySettings $userSettings, User $user): ProjectCollection
    {
        $projects = [];

        $portalAssetResourceProjects = $this->portalDetailExtension->portalAssetResourceProjects($assetResource);

        if (!empty($portalAssetResourceProjects)) {
            $projectInfoSectionCollection = ($this->buildProjectInfoSectionCollection)($userSettings);

            foreach ($portalAssetResourceProjects as $portalAssetResourceProject) {
                if (!($portalAssetResourceProject instanceof Project)) {
                    continue;
                }

                $categoryManagersTableRowLabel = '';
                $categoryManagers = '';

                $projectNameTableRowLabel = '';
                $projectName = '';
                $projectNameLink = '';

                $pimProjectNumberTableRowLabel = '';
                $pimProjectNumber = '';
                $pimProjectNumberLink = '';

                $froqProjectNumberTableRowLabel = '';
                $froqProjectNumber = '';
                $froqProjectNumberLink = '';

                $customerTableRowLabel = '';
                $customerName = '';
                $customerNameLink = '';

                $froqProjectNameTableRowLabel = '';
                $froqProjectName = '';
                $froqProjectNameLink = '';

                if ($projectInfoSectionCollection->getItemByName(ProjectInfoSectionItems::CategoryManagers->readable())?->isEnabled) {
                    $categoryManagersTableRowLabel = (string) $this->assetDetailSettingsManager->getAvailableStructuredTableRowLabel(
                        $userSettings,
                        StructuredTableNames::ProjectSectionItems->readable(),
                        ProjectInfoSectionItems::CategoryManagers->readable()
                    );

                    $categoryManagers = $this->portalDetailExtension->portalProjectCategoryManagers($portalAssetResourceProject);
                }

                if ($projectInfoSectionCollection->getItemByName(ProjectInfoSectionItems::ProjectName->readable())?->isEnabled) {
                    $projectNameTableRowLabel = (string) $this->assetDetailSettingsManager->getAvailableStructuredTableRowLabel(
                        $userSettings,
                        StructuredTableNames::ProjectSectionItems->readable(),
                        ProjectInfoSectionItems::ProjectName->readable()
                    );

                    $isProjectNameAvailableForUser = $portalAssetResourceProject->getName() &&
                        $this->assetLibraryExtension->isFilterAvailableForUser(
                            $user, ProjectInfoSectionItems::ProjectName->readable()
                        );

                    $projectName = $isProjectNameAvailableForUser &&
                        $portalAssetResourceProject->getName()
                        ? (string) $portalAssetResourceProject->getName() : '-';

                    $projectNameLink = $isProjectNameAvailableForUser ? ($this->getBaseUrl)() . $this->urlGenerator->generate('froq_portal_api.assets', [
                            'code' => $user->getCode(), 'filters' => [
                                ProjectInfoSectionItems::ProjectName->readable() => [strtolower($projectName)]
                            ]
                        ]) : '';
                }

                if ($projectInfoSectionCollection->getItemByName(ProjectInfoSectionItems::ProjectPimProjectNumber->readable())?->isEnabled) {
                    $pimProjectNumberTableRowLabel = (string) $this->assetDetailSettingsManager->getAvailableStructuredTableRowLabel(
                        $userSettings,
                        StructuredTableNames::ProjectSectionItems->readable(),
                        ProjectInfoSectionItems::ProjectPimProjectNumber->readable()
                    );

                    $isPimProjectNumberAvailableForUser = $portalAssetResourceProject->getPim_project_number() &&
                        $this->assetLibraryExtension->isFilterAvailableForUser(
                            $user, ProjectInfoSectionItems::ProjectPimProjectNumber->readable()
                        );

                    $pimProjectNumber = $isPimProjectNumberAvailableForUser &&
                        $portalAssetResourceProject->getPim_project_number()
                        ? (string) $portalAssetResourceProject->getPim_project_number() : '-';

                    $pimProjectNumberLink = $isPimProjectNumberAvailableForUser ? ($this->getBaseUrl)() . $this->urlGenerator->generate('froq_portal_api.assets', [
                            'code' => $user->getCode(), 'filters' => [
                                ProjectInfoSectionItems::ProjectPimProjectNumber->readable() => [strtolower($pimProjectNumber)]
                            ]
                        ]) : '';
                }

                if ($projectInfoSectionCollection->getItemByName(ProjectInfoSectionItems::ProjectFroqProjectNumber->readable())?->isEnabled) {
                    $froqProjectNumberTableRowLabel = (string) $this->assetDetailSettingsManager->getAvailableStructuredTableRowLabel(
                        $userSettings,
                        StructuredTableNames::ProjectSectionItems->readable(),
                        ProjectInfoSectionItems::ProjectFroqProjectNumber->readable()
                    );

                    $isFroqProjectNumberAvailableForUser = $portalAssetResourceProject->getFroq_project_number() &&
                        $this->assetLibraryExtension->isFilterAvailableForUser(
                            $user, ProjectInfoSectionItems::ProjectFroqProjectNumber->readable()
                        );

                    $froqProjectNumber = $isFroqProjectNumberAvailableForUser &&
                        $portalAssetResourceProject->getFroq_project_number()
                        ? (string) $portalAssetResourceProject->getFroq_project_number() : '-';

                    $froqProjectNumberLink = $isFroqProjectNumberAvailableForUser ? ($this->getBaseUrl)() . $this->urlGenerator->generate('froq_portal_api.assets', [
                            'code' => $user->getCode(), 'filters' => [
                                ProjectInfoSectionItems::ProjectFroqProjectNumber->readable() => [strtolower($froqProjectNumber)]
                            ]
                        ]) : '';
                }

                if ($projectInfoSectionCollection->getItemByName(ProjectInfoSectionItems::Customer->readable())?->isEnabled) {
                    $customerTableRowLabel = (string) $this->assetDetailSettingsManager->getAvailableStructuredTableRowLabel(
                        $userSettings,
                        StructuredTableNames::ProjectSectionItems->readable(),
                        ProjectInfoSectionItems::Customer->readable()
                    );

                    $isCustomerAvailableForUser = $portalAssetResourceProject->getCustomer() &&
                        $portalAssetResourceProject->getCustomer()->getName() &&
                        $this->assetLibraryExtension->isFilterAvailableForUser(
                            $user, ProjectInfoSectionItems::ProjectFroqProjectNumber->readable()
                        );

                    $customerName = $isCustomerAvailableForUser &&
                        $portalAssetResourceProject->getCustomer()?->getName()
                        ? (string) $portalAssetResourceProject->getCustomer()->getName() : '-';

                    $customerNameLink = $isCustomerAvailableForUser ? ($this->getBaseUrl)() . $this->urlGenerator->generate('froq_portal_api.assets', [
                            'code' => $user->getCode(), 'filters' => [
                                ProjectInfoSectionItems::ProjectFroqProjectNumber->readable() => [strtolower($customerName)]
                            ]
                        ]) : '';
                }

                if ($projectInfoSectionCollection->getItemByName(ProjectInfoSectionItems::ProjectFroqName->readable())?->isEnabled) {
                    $froqProjectNameTableRowLabel = (string) $this->assetDetailSettingsManager->getAvailableStructuredTableRowLabel(
                        $userSettings,
                        StructuredTableNames::ProjectSectionItems->readable(),
                        ProjectInfoSectionItems::ProjectFroqName->readable()
                    );

                    $isProjectNameAvailableForUser = $portalAssetResourceProject->getFroq_name() &&
                        $portalAssetResourceProject->getFroq_project_number() &&
                        $this->assetLibraryExtension->isFilterAvailableForUser(
                            $user, ProjectInfoSectionItems::ProjectFroqName->readable()
                        );

                    $froqProjectName = $isProjectNameAvailableForUser &&
                        $portalAssetResourceProject->getFroq_name()
                        ? (string) $portalAssetResourceProject->getFroq_name() : '-';

                    $froqProjectNameLink = $isProjectNameAvailableForUser ? ($this->getBaseUrl)() . $this->urlGenerator->generate('froq_portal_api.assets', [
                            'code' => $user->getCode(), 'filters' => [
                                ProjectInfoSectionItems::ProjectFroqName->readable() => [strtolower($froqProjectName)]
                            ]
                        ]) : '';
                }

                $projects[] = new ProjectItem(
                    id: (int) $portalAssetResourceProject->getId(),
                    categoryManagersTableRowLabel: $categoryManagersTableRowLabel,
                    categoryManagers: $categoryManagers,
                    projectNameTableRowLabel: $projectNameTableRowLabel,
                    projectName: $projectName,
                    projectNameLink: $projectNameLink,
                    pimProjectNumberTableRowLabel: $pimProjectNumberTableRowLabel,
                    pimProjectNumber: $pimProjectNumber,
                    pimProjectNumberLink: $pimProjectNumberLink,
                    froqProjectNumberTableRowLabel: $froqProjectNumberTableRowLabel,
                    froqProjectNumber: $froqProjectNumber,
                    froqProjectNumberLink: $froqProjectNumberLink,
                    customerTableRowLabel: $customerTableRowLabel,
                    customerName: $customerName,
                    customerNameLink: $customerNameLink,
                    froqProjectNameTableRowLabel: $froqProjectNameTableRowLabel,
                    froqProjectName: $froqProjectName,
                    froqProjectNameLink: $froqProjectNameLink,
                );
            }
        }

        return new ProjectCollection(
            totalCount: count($projects),
            assetDetailConfigLabel: (string) $this->assetDetailSettingsManager->getAvailableSectionLabel(
                $userSettings, SectionFieldTitles::ProjectSectionTitle->readable()
            ),
            items: $projects
        );
    }
}
