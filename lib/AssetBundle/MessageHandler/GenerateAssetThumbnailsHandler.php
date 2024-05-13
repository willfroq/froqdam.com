<?php

namespace Froq\AssetBundle\MessageHandler;

use Froq\AssetBundle\Message\GenerateAssetThumbnailsMessage;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Document\ImageThumbnail;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Asset\Image\Thumbnail;
use Pimcore\Tool\Storage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GenerateAssetThumbnailsHandler implements MessageHandlerInterface
{
    public function __construct(private readonly ApplicationLogger $logger)
    {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(GenerateAssetThumbnailsMessage $message): void
    {
        $assetId = $message->getAssetId();
        $asset = Asset::getById($assetId);

        if (!$asset) {
            $this->logger->error(sprintf('Asset with ID %d not found.', $assetId));

            return;
        }

        $thumbnailsToGenerate = $this->getThumbnailsToGenerate($message);

        foreach ($thumbnailsToGenerate as $thumbnailName) {
            $this->processThumbnail($asset, $thumbnailName, $message->getForce());
        }
    }

    /**
     * @throws \Exception
     *
     * @return array<int, string>
     */
    private function getThumbnailsToGenerate(GenerateAssetThumbnailsMessage $message): array
    {
        $requestedThumbnails = $message->getThumbnails();
        if (empty($requestedThumbnails)) {
            $thumbnailList = new Asset\Image\Thumbnail\Config\Listing();
            $thumbnailList = $thumbnailList->getThumbnails();

            return array_map(function ($thumbnailConfig) {
                return $thumbnailConfig->getName();
            }, $thumbnailList);
        }

        return array_filter($requestedThumbnails, function ($thumbnailName) {
            if (!Image\Thumbnail\Config::getByName($thumbnailName)) {
                $this->logger->warning(sprintf('Thumbnail config "%s" does not exist', $thumbnailName));

                return false;
            }

            return true;
        });
    }

    private function processThumbnail(Asset $asset, string $thumbnailName, bool $force): void
    {
        if ($force) {
            $asset->clearThumbnail($thumbnailName);
        }

        if (!$this->isThumbnailFolderExists($asset, $thumbnailName)) {
            $thumbnail = $this->generateThumbnail($asset, $thumbnailName);

            if ($thumbnail) {
                $thumbnail->getPath(false);

                // triggers fetching the thumbnail info and updating the asset cache table if width or height are not in the cache
                $thumbnail->getDimensions();
            }
        }
    }

    private function generateThumbnail(Asset $asset, string $thumbnailName): Thumbnail|ImageThumbnail|null
    {
        if ($asset instanceof Asset\Image) {
            return $asset->getThumbnail($thumbnailName);
        } elseif ($asset instanceof Asset\Document) {
            return $asset->getImageThumbnail($thumbnailName);
        }

        return null;
    }

    private function isThumbnailFolderExists(Asset $asset, string $thumbnailConfigName): bool
    {
        $location = sprintf('%s/%s/image-thumb__%s__%s', $asset->getRealPath(), $asset->getId(), $asset->getId(), $thumbnailConfigName);

        return Storage::get('thumbnail')->directoryExists($location);
    }
}
