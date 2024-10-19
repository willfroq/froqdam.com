<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class IsFileBase64 extends Constraint
{
    public string $message = 'This file is not a valid base64 file: \'$fileContents\' .';
}
