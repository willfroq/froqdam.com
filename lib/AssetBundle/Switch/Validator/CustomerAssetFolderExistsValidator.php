<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Validator;

use Froq\AssetBundle\Switch\Enum\AssetResourceOrganizationFolderNames;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class CustomerAssetFolderExistsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        assert(assertion: $constraint instanceof CustomerAssetFolderExists);

        $folderNames = array_column(array: AssetResourceOrganizationFolderNames::cases(), column_key: 'name');

        $isValidFolderName = in_array(needle: $value, haystack: [...$folderNames, '3D_Model_Library']);

        if (!$isValidFolderName) {
            $this->context->buildViolation($constraint->message, ['$customerAssetFolder' => $value])
                ->addViolation();
        }
    }
}
