<?php

declare(strict_types=1);

namespace Froq\WebpackMixBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class MixExtension.
 */
class MixExtension extends AbstractExtension
{
    const MANIFEST_FILE_NAME = 'mix-manifest.json';

    const PUBLIC_DIR_NAME = 'public';

    public function __construct(private readonly KernelInterface $kernel, protected RequestStack $requestStack)
    {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('mix', [$this, 'mix']),
        ];
    }

    /**
     * Get the mix file from the manifest
     *
     * @param string $assetPath The needed asset
     * @param bool   $relative
     *
     * @return string
     *
     * @throws \Exception
     */
    public function mix(string $assetPath, bool $relative = false): string
    {
        if (!str_starts_with('/', $assetPath)) {
            $assetPath = '/'.$assetPath;
        }

        $manifest = $this->readManifest();

        if (!array_key_exists($assetPath, $manifest)) {
            throw new \Exception(
                sprintf(
                    'The "%s" key could not be found in the manifest file. %s',
                    $assetPath,
                    'Please pass just the asset filename as a parameter to the mix() method.'
                )
            );
        }

        $versionedPath = $manifest[$assetPath];

        if(file_exists($this->getPublicDir().'/hot')) {
            return "http://localhost:3000{$versionedPath}";
        }

        if ($relative) {
            return $versionedPath ?? '';
        }

        return $this->getBaseUrl().$versionedPath;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName(): string
    {
        return 'mix';
    }

    /**
     * Read the manifest file if exists
     *
     * @return array<int, string|null>
     *
     * @throws \Exception
     */
    private function readManifest(): array
    {
        static $manifest;

        if (!$manifest) {
            $manifestPath = sprintf('%s'.DIRECTORY_SEPARATOR.'%s', $this->getPublicDir(), self::MANIFEST_FILE_NAME);

            if (!file_exists($manifestPath)) {
                throw new \Exception(
                    'The Mix manifest file does not exist. '.
                    'Please run "npm run webpack" and try again.'
                );
            }

            $manifest = json_decode((string) file_get_contents($manifestPath), true);
        }

        return $manifest;
    }

    private function getBaseUrl(): string
    {
        static $baseUrl;

        if (!$baseUrl) {
            $request = $this->requestStack->getCurrentRequest();

            $baseUrl = $request?->getScheme().'://'.$request?->getHttpHost();
        }

        return $baseUrl;
    }

    private function getPublicDir(): string
    {

        static $publicDir;

        if (!$publicDir) {
            $publicDir = $this->kernel->getProjectDir().DIRECTORY_SEPARATOR.self::PUBLIC_DIR_NAME;
        }

        return $publicDir;
    }
}
