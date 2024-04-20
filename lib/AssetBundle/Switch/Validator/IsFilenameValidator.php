<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class IsFilenameValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        assert(assertion: $constraint instanceof IsFilename);

        $isFilename = preg_match('/^[a-zA-Z0-9._-]+$/', $value) && preg_match('/\./', $value);

        if (!$isFilename) {
            $this->context->buildViolation($constraint->message, ['$givenFilename' => $value])
                ->addViolation();
        }
    }
}
