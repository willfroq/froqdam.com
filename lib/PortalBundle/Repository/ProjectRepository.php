<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Repository;

use Pimcore\Model\DataObject\Project;

final class ProjectRepository
{
    /** @param array<int, Project> $existingProjects */
    public function isPayloadProjectCodeExistsInExistingProjects(array $existingProjects, string $projectCode, string $froqProjectNumber, string $pimProjectNumber): bool
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
            if ($project->getPim_project_number() === $pimProjectNumber) {
                return true;
            }
        }

        return false;
    }

    /** @param array<int, Project> $existingProjects */
    public function getProjectFromExistingProjects(array $existingProjects, string $projectCode, string $froqProjectNumber, string $pimProjectNumber): ?Project
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
            if ($project->getPim_project_number() === $pimProjectNumber) {
                return $project;
            }
        }

        return null;
    }

    public function getByProjectCode(string $code): ?Project
    {
        $project = (new Project\Listing())
            ->addConditionParam('Code = ?', $code)
            ->current();

        if (!($project instanceof Project)) {
            return null;
        }

        return $project;
    }

    public function getByPimProjectNumber(string $code): ?Project
    {
        $project = (new Project\Listing())
            ->addConditionParam('pim_project_number = ?', $code)
            ->current();

        if (!($project instanceof Project)) {
            return null;
        }

        return $project;
    }

    public function getByFroqProjectNumber(string $code): ?Project
    {
        $project = (new Project\Listing())
            ->addConditionParam('froq_project_number = ?', $code)
            ->current();

        if (!($project instanceof Project)) {
            return null;
        }

        return $project;
    }
}
