<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Validator;

use Froq\PortalBundle\Repository\OrganizationRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class OrganizationKeyAndNameMustBeEqualValidator extends ConstraintValidator
{
    public function __construct(private readonly OrganizationRepository $organizationRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        assert(assertion: $constraint instanceof OrganizationKeyAndNameMustBeEqual);

        $organization = $this->organizationRepository->getByOrganizationCode((string) $value);

        if ($organization?->getName() !== $organization?->getKey()) {
            $this->context->buildViolation($constraint->message, ['$organizationCode' => $value])
                ->addViolation();
        }
    }
}
