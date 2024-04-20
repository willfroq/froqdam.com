<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class IsJsonMaxThreeLevelsDeepValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        assert(assertion: $constraint instanceof IsJsonMaxThreeLevelsDeep);

        $data = json_decode($value, true);

        if (($data === null && json_last_error() !== JSON_ERROR_NONE) || !is_array($data)) {
            $this->context->buildViolation($constraint->message, ['$keyName' => (string) $data])
                ->addViolation();
        }

        $this->isMaxThreeLevelsDeep($constraint, $data);
    }

    /** @param  array<int|string, mixed>|null $data */
    private function isMaxThreeLevelsDeep(IsJsonMaxThreeLevelsDeep $constraint, ?array $data, int $level = 1): bool
    {
        foreach ($data ?? [] as $key => $value) {
            if (is_array($value)) {
                if ($level > 3) {
                    $this->context->buildViolation($constraint->message, ['$keyName' => (string) $key])
                        ->addViolation();
                }
                if (!$this->isMaxThreeLevelsDeep($constraint, $value, $level + 1)) {
                    return false;
                }
            }
        }

        return true;
    }
}
