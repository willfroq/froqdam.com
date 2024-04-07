<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Enum;

enum ProjectInfoSectionItems: int
{
    case CategoryManagers = 1;
    case ProjectPimProjectNumber = 2;
    case ProjectName = 3;
    case ProjectFroqProjectNumber = 4;
    case Customer = 5;
    case ProjectFroqName = 6;

    public function readable(): string
    {
        return match ($this) {
            self::CategoryManagers => 'category_managers',
            self::ProjectPimProjectNumber => 'project_pim_project_number',
            self::ProjectName => 'project_name',
            self::ProjectFroqProjectNumber => 'project_froq_project_number',
            self::Customer => 'customer',
            self::ProjectFroqName => 'project_froq_name',
        };
    }
}
