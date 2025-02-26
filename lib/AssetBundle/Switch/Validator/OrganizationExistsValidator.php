<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Validator;

use Froq\PortalBundle\Repository\OrganizationRepository;
use Pimcore\Model\DataObject\Organization;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class OrganizationExistsValidator extends ConstraintValidator
{
    public function __construct(private readonly OrganizationRepository $organizationRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        assert(assertion: $constraint instanceof OrganizationExists);

        $organization = $this->organizationRepository->getByOrganizationCode((string) $value);

        if (!($organization instanceof Organization)) {
            $this->context->buildViolation($constraint->message, ['$organizationCode' => $value])
                ->addViolation();
        }
    }
}
