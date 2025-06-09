<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ColourLibrary\Controller;

use Pimcore\Model\Asset;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/download-file/{id<\d+>}', name: 'froq_portal.colour_library.asset.file', methods: [Request::METHOD_GET])]
final class DownloadAssetFileController extends AbstractController
{
    public function __invoke(int $id): StreamedResponse
    {
        $asset = Asset::getById($id);

        if (!($asset instanceof Asset)) {
            throw $this->createNotFoundException('File not found');
        }

        $fileStream = $asset->getStream();

        if (!is_resource($fileStream)) {
            throw $this->createNotFoundException('File not found');
        }

        $response = new StreamedResponse(callback: function () use ($fileStream) {
            $outputStream = fopen(filename: 'php://output', mode: 'wb');

            if (!is_resource($outputStream)) {
                throw $this->createNotFoundException('File not found');
            }

            stream_copy_to_stream(from: $fileStream, to: $outputStream);
        });

        $response->headers->set(key: 'Content-Type', values: $asset->getMimeType());

        $disposition = HeaderUtils::makeDisposition(disposition: HeaderUtils::DISPOSITION_ATTACHMENT, filename: (string) $asset->getFilename());

        $response->headers->set(key: 'Content-Disposition', values: $disposition);

        return $response;
    }
}
