<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Action;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

final class GetS3PrefixName
{
    public function __construct(private readonly string $projectDirectory, private readonly KernelInterface $kernel)
    {
    }

    public function __invoke(): string
    {
        $configPath = $this->projectDirectory . "/config/packages/{$this->kernel->getEnvironment()}/flysystem.yaml";

        return (string) Yaml::parseFile($configPath)['flysystem']['storages']['pimcore.asset.storage']['options']['prefix'];
    }
}
