<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Action;

use Froq\AssetBundle\Model\DataObject\AssetDocument;
use Pimcore\Model\Asset;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class BuildFileAsset
{
    /**
     * @throws \Exception
     */
    public function __invoke(UploadedFile $uploadedFile, string $filename, Asset\Folder $assetFolderParent): AssetDocument|Asset|Asset\Image|null
    {
        $stream = fopen($uploadedFile->getPathname(), 'r');

        $fileExtension = (string) $uploadedFile->guessExtension();

        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tif', 'tiff', 'heic', 'heif'];

        $asset = null;

        if (is_resource($stream)) {
            $asset = match (true) {
                str_contains(haystack: $fileExtension, needle: 'pdf') => (function () use ($filename, $assetFolderParent, $stream) {
                    $asset = new AssetDocument();
                    $asset->setFilename($filename);
                    $asset->setParent($assetFolderParent);
                    $asset->setData((string) stream_get_contents($stream));
                    $asset->save();

                    return $asset;
                })(),

                in_array(needle: $fileExtension, haystack: $imageExtensions) => (function () use ($filename, $assetFolderParent, $stream) {
                    $asset = new Asset\Image();
                    $asset->setFilename($filename);
                    $asset->setParent($assetFolderParent);
                    $asset->setData((string) stream_get_contents($stream));
                    $asset->save();

                    return $asset;
                })(),

                default => (function () use ($filename, $assetFolderParent, $stream) {
                    $asset = new Asset();
                    $asset->setFilename($filename);
                    $asset->setParent($assetFolderParent);
                    $asset->setData((string) stream_get_contents($stream));
                    $asset->save();

                    return $asset;
                })()
            };

            fclose($stream);
        }

        return $asset;
    }
}
