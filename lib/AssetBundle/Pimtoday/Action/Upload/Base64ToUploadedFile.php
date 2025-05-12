<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Action\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final class Base64ToUploadedFile
{
    public function __invoke(string $base64, string $filename = 'upload.bin'): ?UploadedFile
    {
        if (preg_match('/^data:(.*);base64,(.*)$/', $base64, $matches)) {
            $base64 = $matches[2];
        }

        $binaryData = base64_decode($base64);
        if ($binaryData === false) {
            return null;
        }

        $tmpFilePath = tempnam(sys_get_temp_dir(), 'upload_');
        file_put_contents($tmpFilePath, $binaryData);

        return new UploadedFile(
            $tmpFilePath,
            $filename,
            null,
            null,
            true
        );

    }
}
