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
final class OrganizationKeyAndNameMustBeEqual extends Constraint
{
    public string $message = 'Organization name and key MUST be the same on orgCode: \'$organizationCode\'! Please reupload!!!';
}
