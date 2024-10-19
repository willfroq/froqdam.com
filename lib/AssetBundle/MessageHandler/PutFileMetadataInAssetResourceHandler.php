<?php

declare(strict_types=1);

namespace Froq\AssetBundle\MessageHandler;

use Froq\AssetBundle\Action\SetFileMetadata;
use Froq\AssetBundle\Message\PutFileMetadataInAssetResourceMessage;
use Froq\AssetBundle\Model\DataObject\AssetDocument;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

#[AsMessageHandler(fromTransport: 'put_file_metadata', handles: PutFileMetadataInAssetResourceMessage::class, method: '__invoke', priority: 10)]
final class PutFileMetadataInAssetResourceHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly SetFileMetadata $setFileMetadata,
        private readonly ApplicationLogger $applicationLogger,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(PutFileMetadataInAssetResourceMessage $putFileMetadataInAssetResourceMessage): void
    {
        try {
            foreach ($putFileMetadataInAssetResourceMessage->parentIds as $parentId) {
                $parentAssetResource = AssetResource::getById($parentId);

                if (!($parentAssetResource instanceof AssetResource)) {
                    continue;
                }

                foreach ($parentAssetResource->getChildren() as $assetResourceChild) {
                    if (!($assetResourceChild instanceof AssetResource)) {
                        continue;
                    }

                    $assetDocument = $assetResourceChild->getAsset();

                    if ($assetDocument instanceof Asset\Image) {
                        ($this->setFileMetadata)($assetDocument, $assetResourceChild);
                    }

                    if ($assetDocument instanceof AssetDocument) {
                        ($this->setFileMetadata)($assetDocument, $assetResourceChild);
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->applicationLogger->error(message: $exception->getMessage());

            throw new \Exception(message: $exception->getMessage() . 'PutFileMetadataInAssetResourceMessage.php line: 57');
        }
    }
}
