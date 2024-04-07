<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Controller;

use Froq\AssetBundle\Manager\AssetResource\AssetResourcePreviewManager;
use Pimcore\Model\Asset\Document;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/asset/preview', name: 'froq.asset.asset_preview.')]
class AssetPreviewController extends AbstractController
{
    /**
     * @param AssetResourcePreviewManager $previewManager
     * @param int $id
     *
     * @return Response
     */
    #[Route('/document/{id}', name: 'document', methods: ['GET'])]
    public function previewDocumentAction(AssetResourcePreviewManager $previewManager, int $id): Response
    {
        $asset = Document::getById($id);

        if (!$asset) {
            throw $this->createNotFoundException('could not load document asset');
        }

        $stream = $previewManager->getDocumentPreviewPdf($asset);
        if ($stream) {
            return new StreamedResponse(function () use ($stream) {
                fpassthru($stream);
            }, 200, [
                'Content-Type' => 'application/pdf',
            ]);
        } else {
            throw $this->createNotFoundException('Unable to get preview for asset ' . $asset->getId());
        }
    }
}
