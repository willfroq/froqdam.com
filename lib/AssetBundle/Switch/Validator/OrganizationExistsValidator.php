<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Validator;

use Pimcore\Model\DataObject\Organization;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class OrganizationExistsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        assert(assertion: $constraint instanceof OrganizationExists);

        $organization = Organization::getByCode((string) $value)->current(); /** @phpstan-ignore-line */
        if (!($organization instanceof Organization)) {
            $this->context->buildViolation($constraint->message, ['$organizationCode' => $value])
                ->addViolation();
        }
    }
}
