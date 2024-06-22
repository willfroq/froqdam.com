<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Utility;

final class CleanupUploadsDirectory
{
    public function __invoke(string $destination): void
    {
        if (!is_dir($destination)) {
            return;
        }

        if ($handle = opendir($destination)) {
            while (false !== ($file = readdir($handle))) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $filePath = $destination . DIRECTORY_SEPARATOR . $file;

                if (!is_file($filePath)) {
                    continue;
                }

                unlink($filePath);
            }

            if (is_resource($handle)) {
                closedir($handle);
            }
        }
    }
}
