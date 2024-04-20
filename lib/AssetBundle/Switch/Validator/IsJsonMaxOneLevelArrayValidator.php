<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class IsJsonMaxOneLevelArrayValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        assert(assertion: $constraint instanceof IsJsonMaxOneLevelArray);

        $data = json_decode($value, true);

        if (($data === null && json_last_error() !== JSON_ERROR_NONE) || !is_array($data)) {
            $this->context->buildViolation($constraint->message, ['$keyName' => (string) $data])
                ->addViolation();
        }

        foreach ($data ?? [] as $key => $item) {
            if (!is_array($item)) {
                $this->context->buildViolation($constraint->message, ['$keyName' => (string) $key])
                    ->addViolation();
            }
        }
    }
}
