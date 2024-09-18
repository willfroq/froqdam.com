<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Handlers;

use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadRequest;
use Froq\AssetBundle\Switch\Controller\Request\SwitchUploadResponse;
use Froq\AssetBundle\Switch\Enum\LogLevelNames;
use Pimcore\Model\DataObject\Organization;

final class OrganizationFoldersErrorHandler
{
    public function __invoke(SwitchUploadRequest $switchUploadRequest, Organization $organization): ?SwitchUploadResponse
    {
        if (empty($organization->getObjectFolder()) ||
            empty($organization->getAssetFolder()) ||
            empty($organization->getAssetResourceFolders())
        ) {
            $message = 'OrganizationFoldersErrorHandler: There\'s no organization AssetFolder or ObjectFolder or AssetResource paths specified. Creating them also failed, please make them manually.';

            $actions[] = $message;
            $actions[] = 'REVERTING TO PREVIOUS STATE!!!';

            return new SwitchUploadResponse(
                eventName: $switchUploadRequest->eventName,
                date: date('F j, Y H:i'),
                logLevel: LogLevelNames::ERROR->name.": $message",
                assetId: '',
                assetResourceId: '',
                relatedObjects: [],
                actions: $actions,
                statistics: []
            );
        }

        return null;
    }
}
