<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Mapper\Action;

use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Project;

final class GetProjectFieldValues
{
    /**
     * @param array<string, mixed> $mapping
     *
     * @return array<int, string>
     */
    public function __invoke(AssetResource $parentAssetResource, array $mapping, string $filterName): array
    {
        if (!in_array(needle: $filterName, haystack: array_keys($mapping))) {
            return [];
        }

        $values = [];

        foreach ($parentAssetResource->getProjects() as $project) {
            if (!($project instanceof Project)) {
                continue;
            }

            $value = match ($filterName) {
                'froq_project_owner' => '', // TODO
                'project_owner' => 'f', // TODO
                'project_froq_name' => $project->getFroq_name() ?? '',
                'project_froq_project_number' => $project->getFroq_project_number() ?? '',
                'project_name' => $project->getName() ?? '',
                'project_pim_project_number' => $project->getPim_project_number() ?? '',

                default => null
            };

            $values[] = $value;
        }

        return array_values(array_unique(array_filter($values, fn (?string $value) => $value !== null)));
    }
}
