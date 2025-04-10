<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Exception\ES;

class ESPropertyMappingException extends \RuntimeException
{
    public const PROPERTY_MAPPING_EXCEPTION = 'ElasticSearch property mapping exception';

    public static function undefinedMethodException(string $propertyName, string $method): ESPropertyMappingException
    {
        return new self(sprintf("Configuration Error in Elasticsearch Property Mapping of '%s'. Call to undefined method '%s'",
            $propertyName,
            $method
        ));
    }
}
