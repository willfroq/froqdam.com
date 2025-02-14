<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Action\Messenger;

use Froq\AssetBundle\Message\FillInAssetResourceFileCreatedAndModifiedDateMessage;
use Froq\AssetBundle\Message\GenerateAssetThumbnailsMessage;
use Froq\AssetBundle\Message\PutFileMetadataInAssetResourceMessage;
use Froq\AssetBundle\Switch\Message\UploadFromSwitch;
use Pimcore\Messenger\AssetUpdateTasksMessage;
use Pimcore\Messenger\CleanupThumbnailsMessage;
use Pimcore\Messenger\GenerateWeb2PrintPdfMessage;
use Pimcore\Messenger\MaintenanceTaskMessage;
use Pimcore\Messenger\OptimizeImageMessage;
use Pimcore\Messenger\SanityCheckMessage;
use Pimcore\Messenger\SearchBackendMessage;
use Pimcore\Messenger\SendNewsletterMessage;
use Pimcore\Messenger\VersionDeleteMessage;
use Pimcore\Messenger\VideoConvertMessage;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Youwe\PimcoreElasticsearchBundle\Message\DeleteElementMessage;
use Youwe\PimcoreElasticsearchBundle\Message\UpdateElementMessage;

final class GetMessageClass
{
    public function __invoke(string $serializedObject): string
    {
        return match (true) {
            str_contains(haystack: $serializedObject, needle: 'GenerateAssetThumbnailsMessage') => GenerateAssetThumbnailsMessage::class,
            str_contains(haystack: $serializedObject, needle: 'UploadFromSwitch') => UploadFromSwitch::class,
            str_contains(haystack: $serializedObject, needle: 'SendEmailMessage') => SendEmailMessage::class,
            str_contains(haystack: $serializedObject, needle: 'DeleteElementMessage') => DeleteElementMessage::class,
            str_contains(haystack: $serializedObject, needle: 'UpdateElementMessage') => UpdateElementMessage::class,
            str_contains(haystack: $serializedObject, needle: 'PutFileMetadataInAssetResourceMessage') => PutFileMetadataInAssetResourceMessage::class,
            str_contains(haystack: $serializedObject, needle: 'FillInAssetResourceFileCreatedAndModifiedDateMessage') => FillInAssetResourceFileCreatedAndModifiedDateMessage::class,
            str_contains(haystack: $serializedObject, needle: 'SendNewsletterMessage') => SendNewsletterMessage::class,
            str_contains(haystack: $serializedObject, needle: 'VideoConvertMessage') => VideoConvertMessage::class,
            str_contains(haystack: $serializedObject, needle: 'CleanupThumbnailsMessage') => CleanupThumbnailsMessage::class,
            str_contains(haystack: $serializedObject, needle: 'SearchBackendMessage') => SearchBackendMessage::class,
            str_contains(haystack: $serializedObject, needle: 'SanityCheckMessage') => SanityCheckMessage::class,
            str_contains(haystack: $serializedObject, needle: 'AssetUpdateTasksMessage') => AssetUpdateTasksMessage::class,
            str_contains(haystack: $serializedObject, needle: 'GenerateWeb2PrintPdfMessage') => GenerateWeb2PrintPdfMessage::class,
            str_contains(haystack: $serializedObject, needle: 'VersionDeleteMessage') => VersionDeleteMessage::class,
            str_contains(haystack: $serializedObject, needle: 'OptimizeImageMessage') => OptimizeImageMessage::class,
            str_contains(haystack: $serializedObject, needle: 'MaintenanceTaskMessage') => MaintenanceTaskMessage::class,

            default => 'No Message Class'
        };
    }
}
