<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action\Filter;

use Froq\PortalBundle\Opensearch\Action\GetYamlConfigFileProperties;
use Froq\PortalBundle\Opensearch\Enum\IndexNames;
use Psr\Cache\InvalidArgumentException;

final class GetFilterTypeByFieldName
{
    public function __construct(private readonly GetYamlConfigFileProperties $getYamlConfigFileProperties)
    {
    }

    /**
     * @throws \Exception
     * @throws InvalidArgumentException
     */
    public function __invoke(string $fieldName): string
    {
        $type = '';

        foreach (($this->getYamlConfigFileProperties)(IndexNames::AssetResourceItem->readable()) as $filterName => $property) {
            $type = $property['type'] ?? '';

            if ($fieldName !== $filterName || empty($type)) {
                continue;
            }

            return $type;
        }

        return $type;
    }
}
