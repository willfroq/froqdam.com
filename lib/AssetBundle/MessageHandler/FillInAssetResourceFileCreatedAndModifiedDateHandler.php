<?php

declare(strict_types=1);

namespace Froq\AssetBundle\MessageHandler;

use Froq\AssetBundle\Manager\AssetResource\AssetResourceFileDateManager;
use Froq\AssetBundle\Message\FillInAssetResourceFileCreatedAndModifiedDateMessage;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

#[AsMessageHandler(fromTransport: 'put_file_dates', handles: FillInAssetResourceFileCreatedAndModifiedDateMessage::class, method: '__invoke', priority: 10)]
final class FillInAssetResourceFileCreatedAndModifiedDateHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly ApplicationLogger $applicationLogger,
        private readonly AssetResourceFileDateManager $assetResourceFileDateManager
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(FillInAssetResourceFileCreatedAndModifiedDateMessage $fileCreatedAndModifiedDateMessage): void
    {
        try {
            foreach ($fileCreatedAndModifiedDateMessage->parentIds as $parentId) {
                $parentAssetResource = AssetResource::getById($parentId);

                if (!($parentAssetResource instanceof AssetResource)) {
                    continue;
                }

                foreach ($parentAssetResource->getChildren() as $assetResourceChild) {
                    if (!($assetResourceChild instanceof AssetResource)) {
                        continue;
                    }

                    $this->assetResourceFileDateManager->updateFileDates($assetResourceChild);
                }
            }
        } catch (\Exception $exception) {
            $this->applicationLogger->error(message: $exception->getMessage());
        }
    }
}
