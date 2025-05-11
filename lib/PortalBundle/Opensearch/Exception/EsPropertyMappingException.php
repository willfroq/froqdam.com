<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Exception;

final class EsPropertyMappingException extends \RuntimeException
{
    public static function wrongConfig(string $propertyName, string $method): EsPropertyMappingException
    {
        return new self(sprintf(
            "Configuration Error in Elasticsearch Property Mapping of '%s'. Call to undefined method '%s'",
            $propertyName, $method
        ));
    }
}
