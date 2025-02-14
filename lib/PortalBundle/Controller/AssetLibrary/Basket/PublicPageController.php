<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller\AssetLibrary\Basket;

use Froq\PortalBundle\Action\GetS3Client;
use Froq\PortalBundle\Action\GetS3PrefixName;
use Froq\PortalBundle\DataTransferObject\Response\DownloadPageResponse;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetBasket;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

final class PublicPageController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/download-public-file/{id<\d+>/{uuid}}', name: 'download_public_file', methods: [Request::METHOD_GET])]
    public function downloadPublicFile(int $id, string $uuid): StreamedResponse
    {
        $assetBasket = AssetBasket::getByUUID($uuid)?->current(); /** @phpstan-ignore-line */

        if (!($assetBasket instanceof AssetBasket)) {
            throw new \Exception(message: 'AssetBasket does not exist.');
        }

        $assetResourceIds = array_map(fn (AssetResource $assetResource) => $assetResource->getId(), $assetBasket->getAssetResources());

        if (!in_array(needle: $id, haystack: $assetResourceIds)) {
            throw $this->createNotFoundException('File not found');
        }

        $asset = AssetResource::getById($id)?->getAsset();

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

    /**
     * @throws \Exception
     */
    #[Route(path: '/download-all-public-files', name: 'download_all_public_files', methods: [Request::METHOD_GET])]
    public function downloadAllPublicFiles(Request $request, GetS3Client $getS3Client, string $s3BucketNameAssets, GetS3PrefixName $getS3PrefixName): Response
    {
        $s3Client = ($getS3Client)();

        $prefix = ($getS3PrefixName)();

        if (empty($prefix)) {
            throw $this->createNotFoundException('File not found');
        }

        $assetBasket = AssetBasket::getByUUID($request->get('uuid'))?->current(); /** @phpstan-ignore-line */
        if (!($assetBasket instanceof AssetBasket)) {
            throw $this->createNotFoundException('File not found');
        }

        $assetResources = array_map(fn (AssetResource $assetResource) => $assetResource, $assetBasket->getAssetResources());

        $preSignedUrls = [];

        foreach ($assetResources as $assetResource) {
            if (!($assetResource instanceof AssetResource)) {
                throw $this->createNotFoundException('File not found');
            }

            $asset = $assetResource->getAsset();

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

    /**
     * @throws \Exception
     */
    #[Route(path: '/download-page', name: 'download_page', methods: [Request::METHOD_GET])]
    public function downloadPage(Request $request): Response
    {
        $assetBasket = AssetBasket::getByUUID($request->get('uuid'))?->current(); /** @phpstan-ignore-line */
        if (!($assetBasket instanceof AssetBasket)) {
            throw new \Exception(message: 'AssetBasket does not exist.');
        }

        if ($assetBasket->getExpirationDate()?->getTimestamp() < time()) {
            throw new \Exception(message: 'AssetBasket has expired.');
        }

        $user = current($assetBasket->getUser());

        if (!($user instanceof User)) {
            throw new \Exception(message: 'AssetBasket must have a user.');
        }

        $assetResources = $assetBasket->getAssetResources();

        return $this->render(
            view: '@FroqPortal/download-page.html.twig',
            parameters: (new DownloadPageResponse(
                assetResources: $assetResources,
                assetResourceIds: array_map(fn (AssetResource $assetResource) => (int) $assetResource->getId(), $assetResources),
                expiryDate: $assetBasket->getExpirationDate()->format('F j, Y H:i'),
                uuid: (string) $request->get('uuid')
            ))->toArray()
        );
    }
}
