<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Exception\ES;

class EsFilterOptionsProviderException extends \RuntimeException
{
    public static function undefinedEsMappingException(string $className, string $optionValue, string $esIndex): EsFilterOptionsProviderException
    {
        return new self(sprintf("Configuration Error in '%s'. There's no Elasticsearch mapping '%s' defined for index '%s'.",
            $className,
            $optionValue,
            $esIndex
        ));
    }
}
