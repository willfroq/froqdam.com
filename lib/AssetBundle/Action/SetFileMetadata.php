<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Action;

use Froq\AssetBundle\Model\DataObject\AssetDocument;
use Froq\AssetBundle\Utility\FileValidator;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;

final class SetFileMetadata
{
    public function __construct(private readonly ApplicationLogger $applicationLogger)
    {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(AssetDocument|Asset\Image $assetDocument, AssetResource $assetResourceChild): void
    {
        $isUpdated = false;

        if ((int) $assetDocument->getFileSize() === 0) {
            return;
        }

        if (empty($assetResourceChild->getEmbeddedMetadata()) && !empty($assetDocument->getEmbeddedMetaData(force: true))) {
            $assetResourceChild->setEmbeddedMetadata((string) json_encode($assetDocument->getEmbeddedMetaData(force: true)));

            $isUpdated = true;
        }

        if (empty($assetResourceChild->getExifData()) && !empty($assetDocument->getExifData())) {
            $assetResourceChild->setExifData((string) json_encode($assetDocument->getExifData()));

            $isUpdated = true;
        }

        if (empty($assetResourceChild->getXmpData()) && !empty($assetDocument->getXMPData())) {
            $assetResourceChild->setXmpData((string) json_encode($assetDocument->getXMPData()));

            $isUpdated = true;
        }

        if (empty($assetResourceChild->getIptcData()) && !empty($assetDocument->getIPTCData())) {
            $assetResourceChild->setIptcData((string) json_encode($assetDocument->getIPTCData()));

            $isUpdated = true;
        }

        if ($assetDocument instanceof AssetDocument) {
            if (empty($assetResourceChild->getPdfText()) && FileValidator::isValidPdf($assetDocument) && !empty($assetDocument->getText())) {
                $assetResourceChild->setPdfText((string) $assetDocument->getText());

                $isUpdated = true;
            }
        }

        if (!$isUpdated) {
            return;
        }

        $assetResourceChild->save();

        $this->applicationLogger->info(message: sprintf('AssetResource file metadata created in assetResourceId: %s', $assetResourceChild->getId()));
    }
}
