<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Exception;

final class NoEsMappingConfiguredException extends \RuntimeException
{
    public static function noConfig(): NoEsMappingConfiguredException
    {
        return new self(message: 'There is no mapping configured.');
    }
}
