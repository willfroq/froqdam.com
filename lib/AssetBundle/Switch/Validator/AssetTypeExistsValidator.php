<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Validator;

use Pimcore\Model\DataObject\AssetType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class AssetTypeExistsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        assert(assertion: $constraint instanceof AssetTypeExists);

        $assetType = AssetType::getByName((string) $value)?->current(); /** @phpstan-ignore-line */
        if (!($assetType instanceof AssetType)) {
            $this->context->buildViolation($constraint->message, ['$assetTypeName' => $value])
                ->addViolation();
        }
    }
}
