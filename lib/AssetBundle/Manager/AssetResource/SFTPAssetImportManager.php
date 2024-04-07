<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Manager\AssetResource;

use Froq\AssetBundle\Exception\AssetImportException;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Folder as AssetFolder;
use Pimcore\Model\Asset\Service as AssetService;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Folder as DataObjectFolder;
use Pimcore\Model\Document\Folder as DocumentFolder;
use Symfony\Component\Filesystem\Filesystem;

class SFTPAssetImportManager
{
    public const ASSETS_SFTP_UPLOADS_PATH = PIMCORE_PRIVATE_VAR . '/sftp_upload/assets'; /** @phpstan-ignore-line */
    public const PIMCORE_DEFAULT_IMPORTED_ASSETS_FOLDER_PATH = '/imported-assets';

    public function __construct(private readonly FileSystem $fileSystem, private readonly ApplicationLogger $logger)
    {
    }

    /**
     * @param AssetResource $assetResource
     *
     * @return void
     *
     * @throws \Exception
     */
    public function importAssetByUploadName(AssetResource $assetResource): void
    {
        try {
            $uploadName = $assetResource->getUploadName();
            if (!$uploadName) {
                return;
            }

            $fileName = basename($uploadName);
            $sftpFilePath = $this->getSftpFilePath($fileName);

            if (!$this->fileSystem->exists($sftpFilePath)) {
                throw AssetImportException::fileDoesntExit($sftpFilePath);
            }

            $data = file_get_contents($sftpFilePath);
            if (!$data) {
                throw AssetImportException::emptyFile($sftpFilePath);
            }

            $pimcoreParentPath = $this->chooseAssetParentPath($uploadName);
            $pimcoreParentFullPath = sprintf('%s/%s', $pimcoreParentPath, $fileName);

            $asset = Asset::getByPath($pimcoreParentFullPath);
            if (!$asset) {
                $asset = $this->createAsset($fileName, $pimcoreParentPath, $data);
            }

            $this->updateAssetResource($assetResource, $asset);

            $this->deleteUploadedAssetFromSFTPFolder($fileName);
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
            throw $exception; // Re-throwing the exception for logging in the context of the Switch integration.
        }
    }

    /**
     *
     * @param string $fileName
     * @param string $parentPath
     * @param string $data
     *
     * @return Asset
     *
     * @throws \Exception
     */
    private function createAsset(string $fileName, string $parentPath, string $data): Asset
    {
        /** @var AssetFolder $parentFolder */
        $parentFolder = $this->getAssetParentFolder($parentPath);

        $asset = new Asset();
        $asset->setFilename($fileName)
            ->setParent($parentFolder)
            ->setData($data)
            ->save();

        return $asset;
    }

    /**
     * @param string $uploadName
     *
     * @return string
     */
    private function chooseAssetParentPath(string $uploadName): string
    {
        $dirname = dirname($uploadName);
        if ($dirname === '.') {
            return self::PIMCORE_DEFAULT_IMPORTED_ASSETS_FOLDER_PATH;
        }

        return $dirname;
    }

    /**
     * @throws \Exception
     */
    private function updateAssetResource(AssetResource $assetResource, Asset $asset): void
    {
        $assetResource->setAsset($asset);
        $assetResource->setUploadName(null);

        $assetResource->save();
    }

    /**
     * @param string $path
     *
     * @return AssetFolder|DocumentFolder|DataObjectFolder
     *
     * @throws \Exception
     */
    private function getAssetParentFolder(string $path): AssetFolder|DocumentFolder|DataObjectFolder
    {
        $folder = AssetFolder::getByPath($path);
        if (!$folder) {
            $folder = AssetService::createFolderByPath($path);
        }

        return $folder;
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    private function getSftpFilePath(string $fileName): string
    {
        return sprintf('%s/%s', self::ASSETS_SFTP_UPLOADS_PATH, $fileName);
    }

    /**
     * @param string $fileName
     *
     * @return void
     */
    private function deleteUploadedAssetFromSFTPFolder(string $fileName): void
    {
        $uploadPath = $this->getSftpFilePath($fileName);

        if ($this->fileSystem->exists($uploadPath)) {
            $this->fileSystem->remove($uploadPath);
        }
    }
}
