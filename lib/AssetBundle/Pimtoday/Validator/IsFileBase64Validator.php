<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class IsFileBase64Validator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        assert(assertion: $constraint instanceof IsFileBase64);

        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $value)) {
            $this->context->buildViolation($constraint->message, ['$fileContents' => $value])
                ->addViolation();
        }
    }
}
