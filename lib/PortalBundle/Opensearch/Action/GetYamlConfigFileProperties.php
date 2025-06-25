<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action;

use Froq\PortalBundle\Opensearch\Exception\NoEsMappingConfiguredException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Cache\CacheInterface;

final class GetYamlConfigFileProperties
{
    public function __construct(private readonly string $projectDirectory, private readonly CacheInterface $cache)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Exception
     * @throws InvalidArgumentException
     */
    public function __invoke(string $indexName): array
    {
        $configPath = $this->projectDirectory."/config/opensearch/{$indexName}_mapping.yaml";

        $mappings = (array) $this->cache->get(key: "{$indexName}_mappings", callback: function (CacheItemInterface $cache) use ($configPath) {
            $cache->expiresAfter(time: 2419200); // 4 weeks

            return Yaml::parseFile($configPath)['mappings'];
        });

        $configFile = $mappings;

        if (!isset($configFile['properties'])) {
            throw NoEsMappingConfiguredException::noConfig();
        }

        return $configFile['properties'];
    }
}
