<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Repository;

use Pimcore\Model\DataObject\Project;

final class ProjectRepository
{
    /** @param array<int, Project> $existingProjects */
    public function isPayloadProjectCodeExistsInExistingProjects(array $existingProjects, string $projectCode, string $froqProjectNumber): bool
    {
        foreach ($existingProjects as $project) {
            if ($project->getCode() === $projectCode) {
                return true;
            }
        }

        foreach ($existingProjects as $project) {
            if ($project->getFroq_project_number() === $froqProjectNumber) {
                return true;
            }
        }

        foreach ($existingProjects as $project) {
            if ($project->getPim_project_number() === $froqProjectNumber) {
                return true;
            }
        }

        return false;
    }

    /** @param array<int, Project> $existingProjects */
    public function getProjectFromExistingProjects(array $existingProjects, string $projectCode, string $froqProjectNumber): ?Project
    {
        foreach ($existingProjects as $project) {
            if ($project->getCode() === $projectCode) {
                return $project;
            }
        }

        foreach ($existingProjects as $project) {
            if ($project->getFroq_project_number() === $froqProjectNumber) {
                return $project;
            }
        }

        foreach ($existingProjects as $project) {
            if ($project->getPim_project_number() === $froqProjectNumber) {
                return $project;
            }
        }

        return null;
    }
}
