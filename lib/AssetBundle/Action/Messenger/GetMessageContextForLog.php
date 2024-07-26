<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Action\Messenger;

use Froq\AssetBundle\Message\GenerateAssetThumbnailsMessage;
use Froq\AssetBundle\Switch\Message\UploadFromSwitch;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Youwe\PimcoreElasticsearchBundle\Message\DeleteElementMessage;
use Youwe\PimcoreElasticsearchBundle\Message\UpdateElementMessage;

final class GetMessageContextForLog
{
    /** @return array<string, mixed> */
    public function __invoke(mixed $messageClass): array
    {
        return match (true) {
            $messageClass instanceof UploadFromSwitch => [
                'customerCode' => $messageClass->customerCode,
                'filename' => $messageClass->filename,
                'messageClass' => UploadFromSwitch::class,
                'queueName' => 'switch_upload'
            ],
            $messageClass instanceof SendEmailMessage => [
                'messageClass' => SendEmailMessage::class,
                'queueName' => 'switch_upload_mailer'
            ],
            $messageClass instanceof GenerateAssetThumbnailsMessage => [
                'assetId' => $messageClass->getAssetId(),
                'messageClass' => GenerateAssetThumbnailsMessage::class,
                'queueName' => 'generate_asset_thumbnail'
            ],
            $messageClass instanceof DeleteElementMessage => [
                'action' => 'asset_delete',
                'messageClass' => DeleteElementMessage::class,
                'queueName' => 'youwe_es_indexing'
            ],
            $messageClass instanceof UpdateElementMessage => [
                'action' => 'asset_update',
                'messageClass' => UpdateElementMessage::class,
                'queueName' => 'youwe_es_indexing'
            ],
            default => ['queueName' => 'pimcore_core, pimcore_image_optimize, pimcore_maintenance']
        };
    }
}
