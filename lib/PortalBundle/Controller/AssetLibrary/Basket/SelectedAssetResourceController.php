<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller\AssetLibrary\Basket;

use Froq\PortalBundle\Action\GetS3Client;
use Froq\PortalBundle\Action\GetS3PrefixName;
use Froq\PortalBundle\DataTransferObject\Request\SelectedAssetResource;
use Froq\PortalBundle\Twig\AssetPreviewExtension;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Project;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

final class SelectedAssetResourceController extends AbstractController
{
    #[Route('/selected-asset-resource', name: 'froq_basket.selected_asset_resource', methods: [Request::METHOD_POST])]
    public function selectedAssetResources(Request $request, AssetPreviewExtension $assetPreviewExtension): Response
    {
        $assetResourceIds = (array) json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw $this->createNotFoundException();
        }

        $selectedAssetResources = [];

        foreach ($assetResourceIds as $assetResourceId) {
            $assetResource = AssetResource::getById($assetResourceId);

            if (!($assetResource instanceof AssetResource)) {
                throw $this->createNotFoundException(message: 'AssetResource not found');
            }

            $asset = $assetResource->getAsset();

            if (!($asset instanceof Asset)) {
                throw $this->createNotFoundException(message: 'File not found');
            }

            $project = current($assetResource->getProjects());

            $selectedAssetResources[] = new SelectedAssetResource(
                id: (int) $assetResource->getId(),
                filename: (string) $assetResource->getAsset()?->getFilename(),
                assetType: (string) $assetResource->getAssetType()?->getName(),
                projectName: $project instanceof Project ? (string) $project->getName() : '',
                thumbnail: $assetPreviewExtension->getAssetThumbnailHashedURL($asset, 'portal_asset_library_item_grid'),
            );
        }

        return $this->json($selectedAssetResources);
    }

    #[Route(path: '/download-all-files', name: 'download_all_files', methods: [Request::METHOD_GET])]
    public function downloadAllFiles(Request $request, GetS3Client $getS3Client, string $s3BucketNameAssets, GetS3PrefixName $getS3PrefixName): Response
    {
        $s3Client = ($getS3Client)();

        $prefix = ($getS3PrefixName)();

        if (empty($prefix)) {
            throw $this->createNotFoundException('File not found');
        }

        $payload = (array) $request->get('assetResourceIds');
        $assetResourceIds = explode(',', current($payload));

        $preSignedUrls = [];

        foreach ($assetResourceIds as $assetResourceId) {
            $asset = AssetResource::getById((int) $assetResourceId)?->getAsset();

            if (!($asset instanceof Asset)) {
                throw $this->createNotFoundException('File not found');
            }

            $fileStream = $asset->getStream();

            if (!is_resource($fileStream)) {
                throw $this->createNotFoundException('File not found');
            }

            $preSignedUrls[] = (string) $s3Client->createPresignedRequest(
                $s3Client->getCommand('GetObject', [
                    'Bucket' => $s3BucketNameAssets,
                    'Key'    => "$prefix{$asset->getRealPath()}{$asset->getFilename()}"
                ]),
                '+20 minutes'
            )->getUri();
        }

        $response = new StreamedResponse(function () use ($preSignedUrls) {
            $outputStream = fopen('php://output', 'wb');

            if (!is_resource($outputStream)) {
                throw $this->createNotFoundException('File not found');
            }

            $zip = new \ZipArchive();
            $zipFile = (string) tempnam(sys_get_temp_dir(), 'batch_');

            $zip->open($zipFile, \ZipArchive::CREATE);

            foreach ($preSignedUrls as $url) {
                $fileStream = fopen($url, 'rb');

                if (!is_resource($fileStream)) {
                    continue;
                }

                $fileContents = (string) stream_get_contents($fileStream);
                fclose($fileStream);

                $fileName = basename((string) parse_url($url, PHP_URL_PATH));
                $zip->addFromString($fileName, $fileContents);
            }

            $zip->close();

            $zipStream = fopen($zipFile, 'rb');

            if (!is_resource($zipStream)) {
                throw $this->createNotFoundException('File not found');
            }

            stream_copy_to_stream($zipStream, $outputStream);

            fclose($zipStream);
            fclose($outputStream);

            unlink($zipFile);
        });

        $downloadFilename = 'batch_download_' . time() . '.zip';

        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', "attachment; filename=$downloadFilename");

        return $response;
    }
}
