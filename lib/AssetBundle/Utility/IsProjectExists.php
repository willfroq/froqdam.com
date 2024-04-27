<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Utility;

use Pimcore\Model\DataObject\Project;

final class IsProjectExists
{
    public function __invoke(string $column, string $value): bool
    {
        return match ($column) {
            'Code' => (fn () => (new Project\Listing())
                    ->addConditionParam('Code = ?', $value)
                    ->current() instanceof Project)(),
            'pim_project_number' => (fn () => (new Project\Listing())
                    ->addConditionParam('pim_project_number = ?', $value)
                    ->current() instanceof Project)(),
            'froq_project_number' => (fn () => (new Project\Listing())
                    ->addConditionParam('froq_project_number = ?', $value)
                    ->current() instanceof Project)(),
            default => false
        };
    }
}
