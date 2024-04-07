<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Manager\AssetResource;

use Pimcore\Model\Asset\Document;

class AssetResourcePreviewManager
{
    /**
     * @param Document $asset
     *
     * @return resource|null
     */
    public function getDocumentPreviewPdf(Document $asset)
    {
        $stream = null;

        if ($asset->getMimeType() == 'application/pdf') {
            $stream = $asset->getStream();
        }

        if (!$stream && $asset->getPageCount() && \Pimcore\Document::isAvailable() && \Pimcore\Document::isFileTypeSupported((string) $asset->getFilename())) {
            try {
                $document = \Pimcore\Document::getInstance();
                $stream = $document?->getPdf($asset);
            } catch (\Exception $e) {
                // nothing to do
            }
        }

        return $stream;
    }
}
