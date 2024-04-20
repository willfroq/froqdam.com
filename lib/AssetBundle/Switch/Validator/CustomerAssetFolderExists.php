<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class CustomerAssetFolderExists extends Constraint
{
    public string $message = 'CustomerAssetFolder \'$customerAssetFolder\' name is not valid. Please make the folder name you want in the admin panel.';
}
