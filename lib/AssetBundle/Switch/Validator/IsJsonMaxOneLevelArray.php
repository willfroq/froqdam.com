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
final class IsJsonMaxOneLevelArray extends Constraint
{
    public string $message = '\'$keyName\' is not a valid JSON structure, maximum one level array allowed. Refer to the docs for the proper json structure.';
}
