<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Validator;

use Froq\PortalBundle\Repository\ProjectRepository;
use Pimcore\Model\DataObject\Project;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ProjectExistsValidator extends ConstraintValidator
{
    public function __construct(private readonly ProjectRepository $projectRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        assert(assertion: $constraint instanceof ProjectExists);

        $data = json_decode($value, true);

        if (($data === null && json_last_error() !== JSON_ERROR_NONE) || !is_array($data)) {
            $this->context->buildViolation($constraint->message, ['$keyName' => (string) $data])
                ->addViolation();
        }

        $projectCode = $data['projectCode'] ?? '';
        $pimProjectNumber = $data['pimProjectNumber'] ?? '';
        $froqProjectNumber = $data['froqProjectNumber'] ?? '';

        $project = $this->projectRepository->getByProjectCode((string) $projectCode);

        if ($project instanceof Project) {
            $this->context->buildViolation($constraint->message, ['$projectCode' => $projectCode])
                ->addViolation();
        }

        $project = $this->projectRepository->getByPimProjectNumber((string) $pimProjectNumber);

        if ($project instanceof Project) {
            $this->context->buildViolation($constraint->message, ['$projectCode' => $pimProjectNumber])
                ->addViolation();
        }

        $project = $this->projectRepository->getByFroqProjectNumber((string) $froqProjectNumber);

        if ($project instanceof Project) {
            $this->context->buildViolation($constraint->message, ['$projectCode' => $froqProjectNumber])
                ->addViolation();
        }
    }
}
