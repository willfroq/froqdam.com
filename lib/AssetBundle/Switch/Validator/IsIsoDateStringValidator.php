<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class IsIsoDateStringValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        assert(assertion: $constraint instanceof IsIsoDateString);

        $isIsoDateString = (bool) preg_match('/^\d{4}-\d{2}-\d{2}(T\d{2}:\d{2}:\d{2}(\.\d+)?(Z|(\+|-)\d{2}:\d{2})?)?$/', $value);

        if (!$isIsoDateString) {
            $this->context->buildViolation($constraint->message, ['$date' => $value])
                ->addViolation();
        }
    }
}
