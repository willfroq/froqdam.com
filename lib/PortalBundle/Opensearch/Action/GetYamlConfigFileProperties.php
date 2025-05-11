<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action;

use Froq\PortalBundle\Opensearch\Exception\NoEsMappingConfiguredException;
use Symfony\Component\Yaml\Yaml;

final class GetYamlConfigFileProperties
{
    public function __construct(private readonly string $projectDirectory)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Exception
     */
    public function __invoke(string $indexName): array
    {
        $configPath = $this->projectDirectory."/config/opensearch/{$indexName}_mapping.yaml";

        if (!isset(Yaml::parseFile($configPath)['mappings'])) {
            throw NoEsMappingConfiguredException::noConfig();
        }

        $configFile = Yaml::parseFile($configPath)['mappings'];

        if (!isset($configFile['properties'])) {
            throw NoEsMappingConfiguredException::noConfig();
        }

        return $configFile['properties'];
    }
}
