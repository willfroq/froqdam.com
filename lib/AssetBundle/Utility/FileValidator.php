<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Utility;

use Pimcore\Model\Asset;

class FileValidator
{
    public static function isValidPdf(?Asset $asset): bool
    {
        if (!$asset) {
            return false;
        }

        $fileExtension = strtolower(pathinfo((string) $asset->getFilename(), PATHINFO_EXTENSION));
        $mimeType = $asset->getMimeType();

        return $fileExtension === 'pdf' && $mimeType === 'application/pdf';
    }
}
